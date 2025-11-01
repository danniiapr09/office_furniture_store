<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Furniture;
use Illuminate\Support\Facades\Storage;

class FurnitureController extends Controller
{
    // ✅ Ambil semua data
    public function index()
    {
        $furnitures = Furniture::all();
        return response()->json($furnitures);
    }

    // ✅ Ambil data berdasarkan ID
    public function show($id)
    {
        $furniture = Furniture::find($id);
        if (!$furniture) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
        return response()->json($furniture);
    }

    // ✅ Tambah data furniture baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('furniture', 'public');
            $validated['image'] = $path;
        }

        $furniture = Furniture::create($validated);

        return response()->json([
            'message' => 'Data furniture berhasil ditambahkan!',
            'data' => $furniture
        ], 201);
    }

    // ✅ Update data furniture
    public function update(Request $request, $id)
    {
        $furniture = Furniture::find($id);
        if (!$furniture) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'kategori' => 'sometimes|required|string|max:100',
            'harga' => 'sometimes|required|numeric',
            'stok' => 'sometimes|required|integer',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Hapus gambar lama & upload baru jika ada file baru
        if ($request->hasFile('image')) {
            if ($furniture->image) {
                Storage::disk('public')->delete($furniture->image);
            }
            $path = $request->file('image')->store('furniture', 'public');
            $validated['image'] = $path;
        }

        $furniture->update($validated);

        return response()->json([
            'message' => 'Data furniture berhasil diupdate!',
            'data' => $furniture
        ]);
    }

    // ✅ Hapus data furniture
    public function destroy($id)
    {
        $furniture = Furniture::find($id);
        if (!$furniture) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        if ($furniture->image) {
            Storage::disk('public')->delete($furniture->image);
        }

        $furniture->delete();

        return response()->json(['message' => 'Data furniture berhasil dihapus!']);
    }
}