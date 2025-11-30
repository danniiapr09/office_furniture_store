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
use Illuminate\Support\Facades\Log;

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

        if (!$userId) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }
        
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

        if (!$userId) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

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
     * Membuat Pesanan Baru dari Data Keranjang.
     * Endpoint: POST /api/orders
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $userId = auth()->id(); 
        
        if (!$userId) {
            Log::warning('Order creation failed: User not authenticated.');
            return response()->json(['message' => 'Anda harus login untuk membuat pesanan.'], 403);
        }

        // 1. Validasi Input (DITAMBAH: contact_phone, shipping_method, payment_method)
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string|max:500',
            'contact_phone' => 'required|string|max:15', // Ditambahkan
            'shipping_method' => 'required|string|max:50', // Ditambahkan
            'payment_method' => 'required|string|max:50', // Ditambahkan
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
        
        Log::info('Order Creation Attempt', ['user_id' => $userId, 'payload' => $validated]);


        try {
            $order = DB::transaction(function () use ($validated, $userId, &$totalAmount) {
                $orderItemsData = [];
                // Mengambil semua harga furnitur dalam satu query
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
                        // Pastikan OrderItem Model juga memiliki $fillable yang lengkap
                    ]);
                }

                // Buat Entri Order Utama (DITAMBAH: contact_phone, shipping_method, payment_method)
                $order = Order::create([
                    'user_id' => $userId,
                    'total_amount' => $totalAmount,
                    'status' => 'Pending', 
                    'shipping_address' => $validated['shipping_address'],
                    'contact_phone' => $validated['contact_phone'],     // Ditambahkan
                    'shipping_method' => $validated['shipping_method'], // Ditambahkan
                    'payment_method' => $validated['payment_method'],   // Ditambahkan
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
            // Log detail error ke file log Laravel
            Log::error('Order creation failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => $userId,
                'payload' => $validated,
            ]);
            
            // Mengembalikan pesan error umum ke frontend
            return response()->json([
                'message' => 'Gagal memproses checkout: Exception: Failed to create order: Gagal membuat pesanan. Silakan coba lagi.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}