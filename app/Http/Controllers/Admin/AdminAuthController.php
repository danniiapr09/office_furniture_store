<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('admin.login');
    }

    // Proses login admin
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Gunakan guard WEB (session)
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            // Cek apakah usernya admin
            if (auth()->user()->role !== 'admin') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akses ditolak. Anda bukan admin.'
                ]);
            }

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.'
        ]);
    }

    // Logout admin
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }
}