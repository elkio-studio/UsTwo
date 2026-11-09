<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

function reportFailure(string $phase, Throwable $error): never
{
    $state = null;
    $reason = null;

    for ($cause = $error; $cause !== null; $cause = $cause->getPrevious()) {
        if (! $cause instanceof PDOException) {
            continue;
        }

        $code = $cause->errorInfo[0] ?? $cause->getCode();
        if (preg_match('/\A[0-9A-Z]{5}\z/', (string) $code)) {
            $state = $code;
        }

        // Only emit fixed labels. PostgreSQL errors can contain connection details.
        $reason = match (true) {
            preg_match('/password authentication failed|tenant or user not found|invalid password/i', $cause->getMessage()) === 1 => 'autenticación rechazada',
            preg_match('/could not translate host name|name or service not known/i', $cause->getMessage()) === 1 => 'host no resuelto',
            preg_match('/timeout expired|connection timed out|connection refused/i', $cause->getMessage()) === 1 => 'servidor no accesible',
            preg_match('/SSL error|SSL is not enabled|does not support SSL/i', $cause->getMessage()) === 1 => 'error TLS',
            default => $reason,
        };
    }

    $details = array_filter([$state === null ? null : "SQLSTATE {$state}", $reason]);
    fwrite(STDERR, "Supabase: fallo en {$phase}".($details ? ' ('.implode(', ', $details).')' : '').".\n");
    exit(1);
}

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Kernel::class);
    $kernel->bootstrap();
    $app['config']->set('logging.default', 'null');
} catch (Throwable $error) {
    reportFailure('inicio de Laravel', $error);
}

try {
    if (DB::getDefaultConnection() !== 'pgsql') {
        throw new RuntimeException('Se requiere PostgreSQL.');
    }

    DB::connection()->select('select 1', [], false);
} catch (Throwable $error) {
    reportFailure('conexión', $error);
}

fwrite(STDOUT, "Supabase: conexión PostgreSQL comprobada.\n");

try {
    $result = $kernel->call('migrate', ['--force' => true, '--no-interaction' => true]);
} catch (Throwable $error) {
    reportFailure('migración', $error);
}

if ($result !== 0) {
    fwrite(STDERR, "Supabase: fallo en migración (Artisan terminó con código {$result}).\n");
    exit(1);
}
