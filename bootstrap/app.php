<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Foundation\Events\DiagnosingHealth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->booted(function (Application $app): void {
        Event::listen(DiagnosingHealth::class, function () use ($app): void {
            if (! $app->isProduction()) {
                return;
            }

            try {
                DB::select('select 1');
            } catch (\Throwable) {
                throw new \RuntimeException('Base de datos no disponible.');
            }
        });
    })
    ->withMiddleware(function (Middleware $middleware): void {
        if (env('RENDER') === 'true' || env('RENDER') === true) {
            $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_PROTO);
        }
        $middleware->web(append: [HandleInertiaRequests::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (QueryException $exception, Request $request) {
            if ($request->is('peliculas', 'peliculas/*') && ! $request->isMethodSafe() && ! $request->expectsJson()) {
                return back(303)->withErrors(['save' => 'No se ha podido guardar el cambio. Puedes volver a intentarlo.']);
            }
        });
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
