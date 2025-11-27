<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * Endpoint: GET /api/categories
     * Mengembalikan daftar kategori untuk dropdown.
     */
    public function index()
    {
        // Ambil hanya ID dan nama kategori
        return response()->json(
            Category::orderBy('name', 'asc')->get(['id', 'name'])
        );
    }

    // Tambahkan method lain jika Anda menggunakan Route::apiResource('categories', ...)
    // Jika tidak, biarkan index() saja untuk memenuhi kebutuhan dropdown
}