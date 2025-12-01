<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Furniture; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan pengguna yang sedang login (Riwayat Pesanan).
     * GET /api/orders
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }
        
        $orders = Order::where('user_id', $userId)
            ->with(['items.furniture', 'payments'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'message' => 'Riwayat pesanan berhasil diambil.',
            'orders'  => $orders,
        ]);
    }

    /**
     * Menampilkan detail satu pesanan.
     * GET /api/orders/{order_id}
     */
    public function show($id)
    {
        $userId = auth()->id();

        if (!$userId) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $order = Order::where('id', $id)
            ->where('user_id', $userId)
            ->with(['items.furniture', 'payments'])
            ->first();

        if (!$order) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan atau Anda tidak memiliki akses.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail pesanan berhasil diambil.',
            'order'   => $order,
        ]);
    }

    /**
     * Membuat pesanan baru.
     * POST /api/orders
     */
    public function store(Request $request)
    {
        $userId = auth()->id(); 
        
        if (!$userId) {
            Log::warning('Order creation failed: User not authenticated.');
            return response()->json(['message' => 'Anda harus login untuk membuat pesanan.'], 403);
        }

        // ----------------------------
        // 1. VALIDASI INPUT
        // ----------------------------
        $validator = Validator::make($request->all(), [
            'shipping_address'        => 'required|string|max:500',
            'contact_phone'           => 'nullable|string|max:15',
            'shipping_method'         => 'nullable|string|max:50',
            'payment_method'          => 'nullable|string|max:50',

            'items'                   => 'required|array|min:1',
            'items.*.furniture_id'    => 'required|integer|exists:furnitures,id',
            'items.*.quantity'        => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated    = $validator->validated();
        $totalAmount  = 0;

        Log::info('Order Creation Attempt', [
            'user_id' => $userId,
            'payload' => $validated,
        ]);

        try {
            // ----------------------------
            // 2. TRANSAKSI DATABASE
            // ----------------------------
            $order = DB::transaction(function () use ($validated, $userId, &$totalAmount) {

                $orderItemsData = [];

                // Ambil semua furniture yang dibutuhkan dalam satu query
                $furnitureIds = collect($validated['items'])
                    ->pluck('furniture_id')
                    ->unique();

                $furnitures = Furniture::whereIn('id', $furnitureIds)
                    ->get()
                    ->keyBy('id');

                foreach ($validated['items'] as $item) {
                    $furnitureId = $item['furniture_id'];
                    $quantity    = $item['quantity'];

                    $furniture = $furnitures->get($furnitureId);

                    if (!$furniture) {
                        throw new \Exception("Furnitur dengan ID {$furnitureId} tidak ditemukan.");
                    }

                    // --- AMBIL HARGA DENGAN AMAN ---
                    // Utama: kolom "price"
                    // Fallback: kolom "harga" kalau project lama
                    $price = $furniture->price ?? $furniture->harga ?? null;

                    if (is_null($price)) {
                        throw new \Exception("Harga furniture dengan ID {$furnitureId} belum di-set di tabel furnitures.");
                    }

                    $subtotal     = $price * $quantity;
                    $totalAmount += $subtotal;

                    $orderItemsData[] = new OrderItem([
                        'furniture_id'   => $furnitureId,
                        'quantity'       => $quantity,
                        'price_per_unit' => $price,
                    ]);
                }

                // ----------------------------
                // 3. BUAT ORDER UTAMA
                // ----------------------------
                $order = Order::create([
                    'user_id'         => $userId,
                    'total_amount'    => $totalAmount,
                    'status'          => 'Pending',
                    'shipping_address'=> $validated['shipping_address'],
                    'contact_phone'   => $validated['contact_phone']   ?? null,
                    'shipping_method' => $validated['shipping_method'] ?? 'JNE Reguler',
                    'payment_method'  => $validated['payment_method']  ?? 'COD',
                ]);

                // ----------------------------
                // 4. SIMPAN ITEM PESANAN
                // ----------------------------
                $order->items()->saveMany($orderItemsData);

                return $order;
            });

            // ----------------------------
            // 5. RESPONSE BERHASIL
            // ----------------------------
            return response()->json([
                'message'      => 'Pesanan berhasil dibuat. Order ID siap untuk pembayaran.',
                'order_id'     => $order->id,
                'total_amount' => $totalAmount,
            ], 201);

        } catch (\Exception $e) {
            // Log detail error ke file log Laravel
            Log::error('Order creation failed: '.$e->getMessage(), [
                'trace'   => $e->getTraceAsString(),
                'user_id' => $userId,
                'payload' => $validated,
            ]);

            // Kirim pesan singkat + detail error untuk debug di Flutter
            return response()->json([
                'message'      => 'Gagal memproses checkout: Failed to create order.',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }
}