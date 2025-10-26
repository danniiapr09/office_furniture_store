<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Furniture;
use Illuminate\Support\Facades\Validator;

class FurnitureController extends Controller
{
    /**
     * 🛋️ Ambil semua furniture
     */
    public function index()
    {
        $furnitures = Furniture::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar semua furniture',
            'data' => $furnitures
        ]);
    }

    /**
     * 🏷️ Ambil furniture berdasarkan kategori
     */
    public function byCategory($id)
    {
        $furnitures = Furniture::where('category_id', $id)->get();

        if ($furnitures->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada furniture untuk kategori ini'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar furniture berdasarkan kategori',
            'data' => $furnitures
        ]);
    }

    /**
     * 🔍 Pencarian lanjutan (berdasarkan nama, kategori, harga, dsb)
     * Contoh: /api/furniture/search?name=sofa&min_price=1000000&max_price=5000000
     */
    public function search(Request $request)
    {
        $query = Furniture::query();

        if ($request->has('name')) {
            $query->where('title', 'like', '%' . $request->name . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $results = $query->get();

        if ($results->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan hasil pencarian'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hasil pencarian furniture',
            'data' => $results
        ]);
    }

    /**
     * 🔎 Pencarian sederhana berdasarkan kata kunci
     * Contoh: /api/furniture/simple-search?q=sofa
     */
    public function simpleSearch(Request $request)
    {
        $keyword = $request->query('q');

        if (!$keyword) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter q (query) diperlukan'
            ], 400);
        }

        $results = Furniture::where('title', 'like', '%' . $keyword . '%')
            ->orWhere('description', 'like', '%' . $keyword . '%')
            ->get();

        if ($results->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada furniture yang cocok dengan pencarian'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Hasil pencarian sederhana',
            'data' => $results
        ]);
    }

    /**
     * ➕ Tambah furniture baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $furniture = Furniture::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Furniture berhasil ditambahkan',
            'data' => $furniture
        ]);
    }

    /**
     * ✏️ Update furniture
     */
    public function update(Request $request, Furniture $furniture)
    {
        $furniture->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Furniture berhasil diperbarui',
            'data' => $furniture
        ]);
    }

    /**
     * 🗑️ Hapus furniture
     */
    public function destroy(Furniture $furniture)
    {
        $furniture->delete();

        return response()->json([
            'success' => true,
            'message' => 'Furniture berhasil dihapus'
        ]);
    }
}
