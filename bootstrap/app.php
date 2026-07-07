<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// Import class middleware Anda di sini
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\CheckPermission;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust ngrok / reverse proxy headers so Laravel can detect HTTPS correctly.
        $middleware->trustProxies(at: '*');

        // Mendaftarkan middleware menggunakan alias
        $middleware->alias([
            'role'       => RoleMiddleware::class,
            'permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Tampilkan halaman error custom untuk HTTP 403 (Akses Ditolak)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            if ($e->getStatusCode() === 403 && !$request->expectsJson()) {
                return response()->view('errors.403', ['exception' => $e], 403);
            }
        });
    })->create();