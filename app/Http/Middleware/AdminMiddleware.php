<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Pastikan sudah login
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        // Pastikan role adalah admin
        if (Auth::user()->role !== 'admin') {
            Auth::logout();
            return redirect()->route('admin.login')->withErrors([
                'email' => 'Anda tidak memiliki akses admin.'
            ]);
        }

        return $next($request);
    }
}