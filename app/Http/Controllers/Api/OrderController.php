<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Furniture; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan pengguna yang sedang login (Riwayat Pesanan).
     * Endpoint: GET /api/orders
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Ambil semua pesanan pengguna, urutkan dari yang terbaru
        $orders = Order::where('user_id', $userId)
                        // Load relasi items dan payment untuk efisiensi
                        ->with(['items.furniture', 'payments']) 
                        ->latest()
                        ->paginate(15); // Gunakan pagination untuk data besar

        return response()->json([
            'message' => 'Riwayat pesanan berhasil diambil.',
            'orders' => $orders
        ]);
    }

    /**
     * Menampilkan detail satu pesanan berdasarkan ID.
     * Endpoint: GET /api/orders/{order_id}
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $userId = auth()->id();

        // Cari pesanan berdasarkan ID dan pastikan dimiliki oleh user yang bersangkutan
        $order = Order::where('id', $id)
                      ->where('user_id', $userId)
                      ->with(['items.furniture', 'payments']) // Load semua relasi
                      ->first();

        if (!$order) {
            return response()->json(['message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.'], 404);
        }

        return response()->json([
            'message' => 'Detail pesanan berhasil diambil.',
            'order' => $order
        ]);
    }

    /**
     * Membuat Pesanan Baru dari Data Keranjang (Metode yang sudah kita buat).
     * Endpoint: POST /api/orders
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string|max:500',
            'items' => 'required|array|min:1', 
            'items.*.furniture_id' => 'required|integer|exists:furnitures,id', 
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $totalAmount = 0;
        $userId = auth()->id(); 

        try {
            $order = DB::transaction(function () use ($validated, $userId, &$totalAmount) {
                $orderItemsData = [];
                $furnitureIds = collect($validated['items'])->pluck('furniture_id')->unique(); 
                $furnitures = Furniture::whereIn('id', $furnitureIds)->get()->keyBy('id'); 

                foreach ($validated['items'] as $item) {
                    $furniture = $furnitures->get($item['furniture_id']);

                    if (!$furniture) {
                        throw new \Exception("Furnitur dengan ID {$item['furniture_id']} tidak ditemukan.");
                    }

                    $price = $furniture->price; 
                    $subtotal = $price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $orderItemsData[] = new OrderItem([
                        'furniture_id' => $furniture->id,
                        'quantity' => $item['quantity'],
                        'price_per_unit' => $price,
                    ]);
                }

                // Buat Entri Order Utama
                $order = Order::create([
                    'user_id' => $userId,
                    'total_amount' => $totalAmount,
                    'status' => 'Pending', 
                    'shipping_address' => $validated['shipping_address'],
                ]);

                // Masukkan Detail Item ke Order
                $order->items()->saveMany($orderItemsData);

                return $order;
            });

            return response()->json([
                'message' => 'Pesanan berhasil dibuat. Order ID siap untuk pembayaran.',
                'order_id' => $order->id,
                'total_amount' => $totalAmount,
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Order creation failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal membuat pesanan. Silakan coba lagi.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}