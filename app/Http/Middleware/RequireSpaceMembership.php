<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSpaceMembership
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()->spaces()->whereKey($request->route('space')->id)->exists(), 404);

        return $next($request);
    }
}
