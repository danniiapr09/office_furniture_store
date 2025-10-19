<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Furniture;
use Illuminate\Http\Request;

class FurnitureController extends Controller
{
    // Get semua kategori
    public function categories()
    {
        $categories = Category::all();
        return response()->json($categories, 200);
    }

    // Get furniture berdasarkan kategori
    public function byCategory($category_id)
    {
        $category = Category::with('furnitures')->find($category_id);

        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json([
            'category' => $category->name,
            'furnitures' => $category->furnitures
        ]);
    }

    // Get semua furniture (tanpa filter)
    public function index()
    {
        $furnitures = Furniture::with('category')->get();
        return response()->json($furnitures);
    }

    // Fitur pencarian furniture
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $categorySlug = $request->input('category');

        $query = \App\Models\Furniture::query()->with('category');

        // 🔍 Filter berdasarkan keyword (nama)
        if ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        // 💰 Filter berdasarkan harga
        if ($minPrice) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // 🏷️ Filter berdasarkan kategori slug
        if ($categorySlug) {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Ambil hasil akhir
        $furnitures = $query->get();

        return response()->json([
            'total' => $furnitures->count(),
            'data' => $furnitures
        ]);
    }

}