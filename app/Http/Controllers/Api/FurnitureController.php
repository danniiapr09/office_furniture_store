<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Furniture;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;

class FurnitureController extends Controller
{
    /**
     * ✅ Ambil semua data furniture
     */
    public function index(Request $request)
    {
        $query = Furniture::with('category');

        // search by name
        if($request->has('q') && $request->q){
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        // optional filter by category
        if($request->has('category') && $request->category){
            $query->where('category_id', $request->category);
        }

        $perPage = 10;
        $furnitures = $query->orderBy('id','desc')->paginate($perPage);

        return response()->json($furnitures);
    }

    /**
     * ✅ Ambil data furniture berdasarkan ID
     */
    public function show($id)
    {
        $furniture = Furniture::find($id);

        if (!$furniture) {
            return response()->json([
                'success' => false,
                'message' => 'Data furniture tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $furniture
        ]);
    }

    /**
     * ✅ Tambah data furniture baru
     */
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
            $validated['image'] = $request->file('image')->store('furniture', 'public');
        }

        $furniture = Furniture::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data furniture berhasil ditambahkan!',
            'data' => $furniture
        ], 201);
    }

    /**
     * ✅ Update data furniture
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
            'kategori' => 'sometimes|required|string|max:100',
            'harga' => 'sometimes|required|numeric',
            'stok' => 'sometimes|required|integer',
            'deskripsi' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Ganti gambar jika ada file baru
        if ($request->hasFile('image')) {
            if ($furniture->image && Storage::disk('public')->exists($furniture->image)) {
                Storage::disk('public')->delete($furniture->image);
            }
            $validated['image'] = $request->file('image')->store('furniture', 'public');
        }

        $furniture->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data furniture berhasil diupdate!',
            'data' => $furniture
        ]);
    }

    /**
     * ✅ Hapus data furniture
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