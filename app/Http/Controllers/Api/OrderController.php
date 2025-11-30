<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Furniture; // Menggunakan Model Furniture sesuai konfirmasi Anda
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Membuat Pesanan Baru dari Data Keranjang.
     * Endpoint: POST /api/orders
     * Membutuhkan: {shipping_address: string, items: [{furniture_id: int, quantity: int}]}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string|max:500',
            // Pastikan item adalah array dan merujuk ke ID furnitur yang ada (exists:furnitures,id)
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
        
        // Ambil ID pengguna yang sedang login (dijamin ada karena middleware auth:sanctum)
        $userId = auth()->id(); 

        try {
            // Menggunakan DB Transaction untuk memastikan atomic operation
            $order = DB::transaction(function () use ($validated, $userId, &$totalAmount) {
                $orderItemsData = [];
                $furnitureIds = collect($validated['items'])->pluck('furniture_id')->unique(); 
                
                // Ambil semua data furnitur yang dibutuhkan
                $furnitures = Furniture::whereIn('id', $furnitureIds)->get()->keyBy('id'); 

                foreach ($validated['items'] as $item) {
                    $furniture = $furnitures->get($item['furniture_id']);

                    if (!$furniture) {
                        // Ini hanya sebagai fallback, seharusnya sudah ditangkap oleh validasi exists:
                        throw new \Exception("Furnitur dengan ID {$item['furniture_id']} tidak ditemukan.");
                    }

                    $price = $furniture->price; 
                    $subtotal = $price * $item['quantity'];
                    $totalAmount += $subtotal;

                    // Buat OrderItem object
                    $orderItemsData[] = new OrderItem([
                        'furniture_id' => $furniture->id,
                        'quantity' => $item['quantity'],
                        'price_per_unit' => $price, // Simpan harga saat ini untuk audit
                    ]);
                }

                // 2. Buat Entri Order Utama
                $order = Order::create([
                    'user_id' => $userId,
                    'total_amount' => $totalAmount,
                    'status' => 'Pending', // Status awal: Menunggu Pembayaran
                    'shipping_address' => $validated['shipping_address'],
                ]);

                // 3. Masukkan Detail Item ke Order
                $order->items()->saveMany($orderItemsData);

                return $order;
            });

            // 4. Kirim Respon Sukses
            return response()->json([
                'message' => 'Pesanan berhasil dibuat. Order ID siap untuk pembayaran.',
                'order_id' => $order->id,
                'total_amount' => $totalAmount,
            ], 201);

        } catch (\Exception $e) {
            // Log error di log file Laravel untuk debugging
            \Log::error('Order creation failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Gagal membuat pesanan. Silakan coba lagi.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}