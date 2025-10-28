<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- PENTING: Tambahkan ini
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek dulu, apakah user sudah login?
        if (!Auth::check()) {
            // Jika belum login, lempar ke halaman login
            return redirect()->route('login');
        }

        // 2. Jika sudah login, cek rolenya
        if (Auth::user()->role == 'admin') {
            // 3. JIKA DIA ADMIN, izinkan lanjut ke tujuan.
            return $next($request);
        }

        // 4. JIKA BUKAN ADMIN, lempar dia ke halaman lain.
        // Misalnya, kita lempar ke dashboard biasa
        return redirect('/')->with('error', 'Anda tidak memiliki hak akses Admin.');
    }
}
