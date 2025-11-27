<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// --- Impor Kelas Middleware yang diperlukan ---
// Kelas Middleware bawaan Breeze harus diimpor di sini
use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
// Middleware Kustom Kita
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Container\Attributes\Authenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Kita memastikan bagian $middleware->web() bersih,
        // karena Breeze Blade tidak selalu memerlukannya secara eksplisit di sini.
        $middleware->web(append: [
             // Anda bisa tambahkan middleware web global di sini jika perlu
        ]);

        $middleware->alias([
            // Gunakan nama kelas yang sudah diimpor di atas
            'auth' => Authenticate::class,
            'guest' => Authenticated::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
