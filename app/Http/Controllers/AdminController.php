<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Furniture;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $categoryCount = Category::count();
        $furnitureCount = Furniture::count();
        return view('admin.dashboard', compact('categoryCount', 'furnitureCount'));
    }

    public function categories()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }

    public function furniture()
    {
        $furnitures = Furniture::with('category')->get();
        return view('admin.furniture', compact('furnitures'));
    }
}
