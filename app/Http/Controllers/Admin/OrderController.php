<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan untuk Admin Panel.
     */
    public function index()
    {
        // Untuk Admin: Ambil semua pesanan dengan relasi yang dibutuhkan
        $orders = Order::with(['user', 'payments'])->latest()->paginate(20);
        
        // Asumsi Anda memiliki folder 'admin' dan file 'orders/index.blade.php'
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail satu pesanan untuk Admin Panel.
     */
    public function show(Order $order)
    {
        // Load relasi penuh sebelum ditampilkan
        $order->load(['user', 'items.furniture', 'payments']);

        // Asumsi Anda memiliki file 'admin/orders/show.blade.php'
        return view('admin.orders.show', compact('order'));
    }
}