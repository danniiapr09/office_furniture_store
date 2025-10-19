<?php

namespace App\Http\Controllers\Api; // PASTIKAN NAMESPACE INI BENAR

use App\Http\Controllers\Controller; // WAJIB DI IMPORT
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use Illuminate\Support\Str; 

class CategoryController extends Controller
{
    public function bySlug($slug)
    {
        $category = Category::where('slug', $slug)->with('furnitures')->first();

        if (!$category) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        return response()->json([
            'category' => $category->name,
            'furnitures' => $category->furnitures
        ]);
    }

}