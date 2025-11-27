<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // <-- PENTING: Tambahkan ini!

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah pengguna sudah login
        if (!Auth::check()) { // <-- Ganti auth()->check() menjadi Auth::check()
            return redirect('login');
        }

        $user = Auth::user(); // <-- Ganti auth()->user() menjadi Auth::user()

        // 2. Cek apakah peran pengguna termasuk dalam array $roles yang diizinkan
        if (!in_array($user->role, $roles)) {
            // Jika tidak diizinkan, kembalikan response 403 Forbidden
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
