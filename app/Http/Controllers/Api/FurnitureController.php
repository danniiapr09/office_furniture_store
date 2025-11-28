<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Furniture;
use Illuminate\Support\Facades\Storage;

class FurnitureController extends Controller
{
    /**
     * Ambil semua furniture (with pagination + image_url)
     */
    public function index(Request $request)
    {
        // Eager load relasi category
        $query = Furniture::with('category'); 

        if ($request->has('q') && $request->q) {
            $query->where('nama', 'like', '%' . $request->q . '%');
        }

        // Filter menggunakan category_id, bukan string 'kategori'
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $furnitures = $query->orderBy('id', 'desc')->paginate(10);

        // Tambahkan image_url ke setiap item
        $furnitures->getCollection()->transform(function ($item) {
            $item->image_url = $item->image ? asset('storage/' . $item->image) : null;
            return $item;
        });

        // Response paginasi standar Laravel sudah cukup
        return response()->json($furnitures);
    }

    /**
     * Ambil detail furniture
     */
    public function show($id)
    {
        // Eager load relasi category untuk detail
        $furniture = Furniture::with('category')->find($id);

        if (!$furniture) {
            return response()->json([
                'success' => false,
                'message' => 'Data furniture tidak ditemukan'
            ], 404);
        }

        $furniture->image_url = $furniture->image ? asset('storage/' . $furniture->image) : null;

        return response()->json([
            'success' => true,
            'data' => $furniture
        ]);
    }

    /**
     * Tambah furniture baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id', // Diubah ke ID kategori
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload image
        if ($request->hasFile('image')) {
            // Karena menggunakan FormData, file akan ada di sini
            $validated['image'] = $request->file('image')->store('furniture', 'public');
        }

        $furniture = Furniture::create($validated);

        // Tambahkan URL absolut setelah dibuat
        $furniture->image_url = $furniture->image ? asset('storage/' . $furniture->image) : null;
        $furniture->load('category'); // Load relasi agar data di response lengkap

        return response()->json([
            'success' => true,
            'message' => 'Data furniture berhasil ditambahkan!',
            'data' => $furniture
        ], 201);
    }

    /**
     * Update furniture
     */
    public function update(Request $request, $id)
    {
        $furniture = Furniture::find($id);

        if (!$furniture) {
            return response()->json([
                'success' => false,
                'message' => 'Data furniture tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|integer|exists:categories,id', // Diubah
            'harga' => 'sometimes|required|numeric',
            'stok' => 'sometimes|required|integer',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Jika ada file baru, hapus file lama lalu upload
        if ($request->hasFile('image')) {
            if ($furniture->image && Storage::disk('public')->exists($furniture->image)) {
                Storage::disk('public')->delete($furniture->image);
            }
            $validated['image'] = $request->file('image')->store('furniture', 'public');
        }

        $furniture->update($validated);

        $furniture->image_url = $furniture->image ? asset('storage/' . $furniture->image) : null;
        $furniture->load('category'); // Load relasi agar data di response lengkap

        return response()->json([
            'success' => true,
            'message' => 'Data furniture berhasil diupdate!',
            'data' => $furniture
        ]);
    }

    /**
     * Hapus furniture
     */
    public function destroy($id)
    {
        $furniture = Furniture::find($id);

        if (!$furniture) {
            return response()->json([
                'success' => false,
                'message' => 'Data furniture tidak ditemukan'
            ], 404);
        }

        // Hapus gambar
        if ($furniture->image && Storage::disk('public')->exists($furniture->image)) {
            Storage::disk('public')->delete($furniture->image);
        }

        $furniture->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data furniture berhasil dihapus!'
        ]);
    }
}