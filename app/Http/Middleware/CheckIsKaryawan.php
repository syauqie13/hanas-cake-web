<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIsKaryawan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user adalah 'karyawan'
        if (Auth::user()->role === 'karyawan') {
            return $next($request);
        }

        // 3. Jika bukan karyawan, arahkan kembali ke halaman lain
        return redirect('/')
            ->with('error', 'Akses ditolak. Hanya karyawan yang dapat mengakses halaman ini.');
    }
}
