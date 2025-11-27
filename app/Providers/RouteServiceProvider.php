<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    // Konstanta redirect spesifik untuk setiap peran
    public const ADMIN_DASHBOARD = '/admin/dashboard';
    public const STAFF_DASHBOARD = '/pegawai/dashboard';
    public const STUDENT_DASHBOARD = '/mahasiswa/dashboard';


    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Fungsi untuk mendapatkan rute tujuan berdasarkan role
     */
    public static function getHomeRoute(string $role): string
    {
        return match ($role) {
            'admin' => self::ADMIN_DASHBOARD,
            'pegawai' => self::STAFF_DASHBOARD,
            'mahasiswa' => self::STUDENT_DASHBOARD,
            default => '/', // Guest atau peran lain diarahkan ke Homepage
        };
    }
}
