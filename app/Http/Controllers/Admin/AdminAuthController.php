<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    // Tampilkan form login admin
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

        // Ambil credential
        $credentials = $request->only('email', 'password');

        // Attempt login pakai guard admin
        if (Auth::guard('admin')->attempt($credentials)) {

            // Sukses login → ke dashboard
            return redirect()->route('admin.dashboard');
        }

        // Login gagal
        return back()->withErrors([
            'email' => 'Email atau password salah.'
        ]);
    }

    // Logout admin
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
