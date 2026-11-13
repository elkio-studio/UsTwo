<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\View\FileViewFinder;

abstract class TestCase extends BaseTestCase
{
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
                        default => $name,
                    });
                }
            };
        });
    }
}
