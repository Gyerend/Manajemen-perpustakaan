<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Mendapatkan pengguna yang sudah login
                $user = Auth::user();

                // Menggunakan logika getHomeRoute yang sudah kita definisikan di RouteServiceProvider
                if ($user) {
                    $role = $user->role;
                    return redirect(RouteServiceProvider::getHomeRoute($role));
                }

                // Fallback ke halaman utama jika role tidak terdefinisi
                return redirect('/');
            }
        }

        return $next($request);
    }
}
