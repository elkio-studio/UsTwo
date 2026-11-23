<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WishUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || self::parts($value) === null) {
            $fail('Introduce una URL HTTP o HTTPS válida, sin credenciales.');
        }
    }

    public static function parts(string $url): ?array
    {
        if (strlen($url) > 2048 || preg_match('/[\x00-\x20\x7f\\\\]/', $url)
            || ! filter_var($url, FILTER_VALIDATE_URL)) return null;
        $parts = parse_url($url);
        if (! is_array($parts) || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
            || isset($parts['user']) || isset($parts['pass'])) return null;
        $host = strtolower(trim($parts['host'] ?? '', '[]'));
        if ($host === '' || str_contains($host, '%')) return null;
        if (! filter_var($host, FILTER_VALIDATE_IP)) {
            if (! preg_match('/^(?=.{1,253}\.?$)[a-z0-9](?:[a-z0-9.-]*[a-z0-9])?\.?$/i', $host)
                || preg_match('/(?:^|\.)(?:0x[0-9a-f]+|[0-9]+)\.?$/i', $host)) return null;
            foreach (explode('.', rtrim($host, '.')) as $label) {
                if ($label === '' || strlen($label) > 63 || str_starts_with($label, '-') || str_ends_with($label, '-')) return null;
            }
        }
        return $parts + ['host' => $host];
    }
}
