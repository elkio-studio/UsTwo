<?php

namespace App\Http\Controllers;

use App\Models\Space;
use Inertia\Inertia;
use Inertia\Response;

class WishController extends Controller
{
    public function index(Space $space): Response
    {
        return Inertia::render('Wishes/Wishes', [
            'spaceId' => $space->id,
            'spaceSlug' => $space->slug,
        ]);
    }
}
