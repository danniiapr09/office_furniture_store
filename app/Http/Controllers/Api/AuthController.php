<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register user baru
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', // ✅ tambah konfirmasi password
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Otomatis login setelah register
        // Pastikan model User menggunakan trait HasApiTokens
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->only(['id', 'name', 'email', 'phone']),
        ], 201);
    }

    /**
     * Login user dan buat token Sanctum
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Cek jika user tidak ada ATAU password salah
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Hapus token lama agar hanya 1 token yang aktif (untuk keamanan)
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Login berhasil',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->only(['id', 'name', 'email', 'phone']),
        ], 200);
    }

    /**
     * Ambil profile user yang login
     */
    public function profile(Request $request)
    {
        // Data diambil melalui middleware 'auth:sanctum'
        return response()->json([
            'user' => $request->user()->only(['id', 'name', 'email', 'phone']),
        ], 200);
    }

    /**
     * Update profile user (Nama dan Telepon)
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update($request->only(['name', 'phone']));

        return response()->json([
            'message' => 'Profile berhasil diperbarui',
            'user'    => $user->only(['id', 'name', 'email', 'phone']),
        ], 200);
    }

    // --- Fungsionalitas Baru ---

    /**
     * Update Email user (membutuhkan password saat ini)
     */
    public function updateEmail(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'email'    => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Cek password saat ini
        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini salah.'
            ], 401);
        }

        $user->email = $request->email;
        $user->save();

        return response()->json([
            'message' => 'Email berhasil diperbarui',
            'user'    => $user->only(['id', 'name', 'email', 'phone']),
        ], 200);
    }
    
    /**
     * Ganti Password user (membutuhkan password lama dan konfirmasi)
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Cek password saat ini
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini salah.'
            ], 401);
        }

        // Update password baru
        $user->password = Hash::make($request->password);
        $user->save();

        // Opsional: Hapus semua token lama (untuk keamanan ekstra setelah ganti password)
        $user->tokens()->delete(); 

        return response()->json([
            'message' => 'Password berhasil diubah. Mohon login ulang.'
        ], 200);
    }


    /**
     * Logout user (hapus token aktif)
     */
    public function logout(Request $request)
    {
        // Menghapus token saat ini yang digunakan untuk request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ], 200);
    }

    /**
     * Ambil data user login (opsional) - sama dengan profile
     */
    public function user(Request $request)
    {
        return $this->profile($request);
    }
}