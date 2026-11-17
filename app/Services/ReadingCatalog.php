<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ReadingCatalog
{
    private const PAGE_SIZE = 12;

    public function search(string $category, string $query, int $page, ?string $volume): array
    {
        $offset = ($page - 1) * self::PAGE_SIZE;

        if ($category === 'books') {
            $result = $this->request('https://openlibrary.org/search.json', [
                'q' => $query, 'limit' => self::PAGE_SIZE, 'offset' => $offset,
                'fields' => 'key,title,author_name,first_publish_year,cover_i',
            ]);

            return $this->results($result['docs'] ?? [], $result['numFound'] ?? 0, $offset, function (array $row): array {
                $id = basename($row['key'] ?? '');
                $author = implode(', ', $row['author_name'] ?? []);
                $year = $row['first_publish_year'] ?? null;
                if (preg_match('/^OL[0-9]+W$/D', $id)) {
                    Cache::put('reading-catalog:open-library:'.$id, [
                        'author' => $author, 'year' => $year, 'cover' => $row['cover_i'] ?? null,
                    ], now()->addMinutes(15));
                }

                return [
                    'id' => $id,
                    'title' => $row['title'] ?? '',
                    'context' => implode(' · ', array_filter([$author, $year])),
                    'cover_url' => $this->bookCover($row['cover_i'] ?? null),
                ];
            });
        }

        if ($category === 'manga') {
            $result = $this->request('https://api.mangaupdates.com/v1/series/search', [
                'search' => $query, 'page' => $page, 'perpage' => self::PAGE_SIZE,
            ], true);

            return $this->results($result['results'] ?? [], $result['total_hits'] ?? 0, $offset, fn ($row) => [
                'id' => (string) ($row['record']['series_id'] ?? ''),
                'title' => $row['record']['title'] ?? '',
                'context' => $row['record']['year'] ?? null,
                'cover_url' => $this->coverUrl($row['record']['image']['url']['thumb'] ?? $row['record']['image']['url']['original'] ?? null),
            ]);
        }

        if ($volume === null) {
            $result = $this->comicVine('search', [
                'query' => $query, 'resources' => 'volume', 'page' => $page,
                'limit' => self::PAGE_SIZE, 'field_list' => 'id,name,start_year,publisher,image',
            ]);

            return $this->results($result['results'], $result['number_of_total_results'] ?? 0, $offset, fn ($row) => [
                'id' => (string) ($row['id'] ?? ''),
                'title' => $row['name'] ?? '',
                'context' => implode(' · ', array_filter([$row['start_year'] ?? null, $row['publisher']['name'] ?? null])),
                'cover_url' => $this->coverUrl($row['image']['original_url'] ?? null),
            ]);
        }

        $filter = 'volume:'.$volume.($query === '' ? '' : ',issue_number:'.$query);
        $result = $this->comicVine('issues', [
            'filter' => $filter, 'offset' => $offset, 'limit' => self::PAGE_SIZE,
            'field_list' => 'id,name,issue_number,cover_date,volume,image',
        ]);

        return $this->results($result['results'], $result['number_of_total_results'] ?? 0, $offset, fn ($row) => [
            'id' => (string) ($row['id'] ?? ''),
            'title' => $this->comicTitle($row),
            'context' => $row['cover_date'] ?? null,
            'cover_url' => $this->coverUrl($row['image']['original_url'] ?? null),
        ]);
    }

    public function preview(string $category, string $id): array
    {
        if ($category === 'books') {
            $work = $this->request('https://openlibrary.org/works/'.$id.'.json');
            $summary = Cache::get('reading-catalog:open-library:'.$id, []);
            $authorNames = [];
            $authorKeys = empty($summary['author'])
                ? array_unique(array_column(array_column($work['authors'] ?? [], 'author'), 'key'))
                : [];
            foreach (array_slice($authorKeys, 0, 5) as $key) {
                if (is_string($key) && preg_match('~^/authors/OL[0-9]+A$~D', $key)) {
                    try {
                        $authorNames[] = $this->request('https://openlibrary.org'.$key.'.json')['name'] ?? '';
                    } catch (ValidationException) {
                        // Preserve the work when an auxiliary author record is unavailable.
                    }
                }
            }
            $cover = $summary['cover'] ?? $work['covers'][0] ?? null;
            $description = $work['description'] ?? null;
            $date = $work['first_publish_date'] ?? null;
            if (! is_string($date) || ! preg_match('/^\d{4}(?:-\d{2}(?:-\d{2})?)?$/D', $date)) {
                $date = $summary['year'] ?? null;
            }

            return $this->record($category, 'open_library', 'work', $id, $work['title'] ?? null,
                is_array($description) ? ($description['value'] ?? null) : $description,
                ($summary['author'] ?? '') ?: implode(', ', array_filter($authorNames)),
                $date,
                $this->bookCover($cover));
        }

        if ($category === 'manga') {
            $series = $this->request('https://api.mangaupdates.com/v1/series/'.$id);
            $authors = collect($series['authors'] ?? [])
                ->filter(fn ($author) => str_contains(strtolower($author['type'] ?? ''), 'author'))
                ->pluck('name')->filter()->unique()->implode(', ');

            return $this->record($category, 'manga_updates', 'series', $id, $series['title'] ?? null,
                $series['description'] ?? null, $authors, $series['year'] ?? null,
                $series['image']['url']['original'] ?? $series['image']['url']['thumb'] ?? null);
        }

        $comic = $this->comicVine('issue/4000-'.$id)['results'];
        $authors = collect($comic['person_credits'] ?? [])
            ->filter(fn ($person) => str_contains(strtolower($person['role'] ?? ''), 'writer'))
            ->pluck('name')->filter()->unique()->implode(', ');

        return $this->record($category, 'comic_vine', 'issue', $id, $this->comicTitle($comic),
            $comic['description'] ?? null, $authors, $comic['cover_date'] ?? null,
            $comic['image']['original_url'] ?? null);
    }

    private function comicTitle(array $row): string
    {
        $volume = $row['volume']['name'] ?? '';
        $number = $row['issue_number'] ?? '';
        $label = trim($volume.($number !== '' ? ' #'.$number : ''));

        return implode(' — ', array_filter([$label, $row['name'] ?? null]));
    }

    private function record(string $category, string $provider, string $type, string $id, mixed $title, mixed $description, mixed $author, mixed $date, mixed $cover): array
    {
        $title = $this->plainText($title, 255);
        if ($title === null) {
            $this->unavailable('La ficha del catálogo no tiene título.');
        }

        $publicationDate = is_scalar($date) ? trim((string) $date) : '';
        if (! preg_match('/^\d{4}(?:-\d{2}(?:-\d{2})?)?$/D', $publicationDate)) {
            $publicationDate = null;
        }

        return [
            'category' => $category,
            'provider' => $provider,
            'record_type' => $type,
            'external_id' => $id,
            'title' => $title,
            'description' => $this->plainText($description, 20000),
            'author' => $this->plainText($author, 255),
            'publication_date' => $publicationDate,
            'cover_url' => $this->coverUrl($cover),
        ];
    }

    private function bookCover(mixed $id): ?string
    {
        return is_numeric($id) && $id > 0 ? 'https://covers.openlibrary.org/b/id/'.$id.'-M.jpg?default=false' : null;
    }

    private function coverUrl(mixed $url): ?string
    {
        return is_string($url) && filter_var($url, FILTER_VALIDATE_URL)
            && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true) ? $url : null;
    }

    private function plainText(mixed $value, int $limit): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $text = trim(html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $text === '' ? null : mb_substr($text, 0, $limit);
    }

    private function results(array $rows, int $total, int $offset, callable $map): array
    {
        return [
            'items' => collect($rows)->filter(fn ($row) => is_array($row))->map($map)
                ->filter(fn ($row) => $row['id'] !== '' && $row['title'] !== '')->values()->all(),
            'hasMore' => $offset + self::PAGE_SIZE < $total,
        ];
    }

    private function comicVine(string $route, array $parameters = []): array
    {
        $key = config('services.comic_vine.key');
        if (! $key) {
            $this->unavailable('El catálogo de cómics no está configurado.');
        }

        $result = $this->request('https://comicvine.gamespot.com/api/'.$route.'/', [
            ...$parameters, 'api_key' => $key, 'format' => 'json',
        ]);

        if ((int) ($result['status_code'] ?? 0) === 107) {
            $this->unavailable('El catálogo ha alcanzado su límite. Inténtalo más tarde.');
        }
        if ((int) ($result['status_code'] ?? 0) !== 1 || ! is_array($result['results'] ?? null)) {
            $this->unavailable();
        }

        return $result;
    }

    private function request(string $url, array $data = [], bool $post = false): array
    {
        try {
            $client = Http::acceptJson()->withUserAgent('UsTwo/1.0')->connectTimeout(3)->timeout(6)
                ->withoutRedirecting();
            $response = $post ? $client->post($url, $data) : $client->get($url, $data);
        } catch (ConnectionException) {
            $this->unavailable();
        }

        if ($response->status() === 429) {
            $this->unavailable('El catálogo ha alcanzado su límite. Inténtalo más tarde.');
        }
        $result = $response->json();
        if (! $response->successful() || ! is_array($result)) {
            $this->unavailable();
        }

        return $result;
    }

    private function unavailable(string $message = 'El catálogo no está disponible.'): never
    {
        throw ValidationException::withMessages(['catalog' => $message]);
    }
}
