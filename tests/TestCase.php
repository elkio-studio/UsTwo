<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\View\FileViewFinder;

abstract class TestCase extends BaseTestCase
{
    protected function patchMetadata(string $url, array $data = [])
    {
        preg_match('~/espacios/\d+/(peliculas|series|lecturas)/(\d+)~', $url, $match);
        $table = ['peliculas' => 'movies', 'series' => 'series', 'lecturas' => 'readings'][$match[1]];
        $version = \Illuminate\Support\Facades\DB::table($table)->where('id', $match[2])->value('metadata_version') ?? 0;

        return $this->patch($url, $data + ['metadata_version' => $version]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Inertia conserva sus nombres de página aunque Vite resuelva archivos renombrados.
        $this->app->extend('inertia.view-finder', function (FileViewFinder $finder) {
            return new class($this->app['files'], $finder->getPaths(), $finder->getExtensions()) extends FileViewFinder
            {
                public function find($name)
                {
                    return parent::find(match ($name) {
                        'Account/Account' => 'Account/AccountSettings/AccountSettings',
                        'Dashboard/Dashboard' => 'Dashboard/ShelfDashboard/ShelfDashboard',
                        'Login/Login' => 'Login/LoginAccessForm/LoginAccessForm',
                        'MovieCreate/MovieCreate' => 'MovieCreate/AddCollectionEntry/AddCollectionEntry',
                        'MovieDetail/MovieDetail' => 'MovieDetail/CollectionEntryDetail/CollectionEntryDetail',
                        'Movies/Movies' => 'Movies/WatchedCollection/WatchedCollection',
                        'Places/Places' => 'Places/PlacesView/PlacesView',
                        'Readings/Readings' => 'Readings/ReadingsView/ReadingsView',
                        'Readings/Create' => 'Readings/ReadingCreate/ReadingCreate',
                        'Readings/Detail' => 'Readings/ReadingDetail/ReadingDetail',
                        'Wishes/Wishes' => 'Wishes/WishesView/WishesView',
                        default => $name,
                    });
                }
            };
        });
    }
}
