<?php

namespace App\Http\Controllers;

class SeriesController extends CollectionController
{
    protected const SERIES = true;
    protected const ROUTE = 'series';
    protected const CANDIDATES = 'series_candidates';
    protected const FOREIGN_KEY = 'series_id';
}
