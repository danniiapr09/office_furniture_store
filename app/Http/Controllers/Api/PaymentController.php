<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment; // Import Payment Model
use Midtrans\Config; 
use Midtrans\Snap;
use Midtrans\Notification; // Import Midtrans Notification
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Gunakan Log untuk debugging Webhook

class PaymentController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false); 
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
    
    /**
     * Inisiasi transaksi pembayaran menggunakan ID Pesanan.
     * Endpoint: POST /api/payments/initiate
     */
    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:orders,id', 
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Order ID tidak valid.', 'errors' => $validator->errors()], 422);
        }

        try {
            $order = Order::with(['user', 'items.furniture'])->findOrFail($request->order_id);

            if ($order->status != 'Pending') {
                return response()->json(['message' => 'Pesanan sudah dibayar atau dibatalkan.'], 400);
            }
            
            $itemDetails = $order->items->map(function ($item) {
                return [
                    'id' => 'FURN-' . $item->furniture_id,
                    'price' => $item->price_per_unit,
                    'quantity' => $item->quantity,
                    'name' => $item->furniture->name ?? 'Produk Furnitur',
                ];
            })->toArray();

            $params = [
                'transaction_details' => [
                    // Order ID Midtrans harus unik. Kita gunakan order_id dan timestamp.
                    'order_id' => $order->id . '-' . time(), 
                    'gross_amount' => (int) $order->total_amount, 
                ],
                'customer_details' => [
                    'first_name' => $order->user->name ?? 'Pelanggan',
                    'email' => $order->user->email ?? 'no-reply@example.com',
                ],
                'item_details' => $itemDetails,
                'callbacks' => [
                    'finish' => env('APP_URL') . '/redirect/finish', 
                    'unfinish' => env('APP_URL') . '/redirect/unfinish', 
                    'error' => env('APP_URL') . '/redirect/error', 
                ]
            ];

            $snapToken = Snap::getSnapToken($params);
            
            return response()->json([
                'message' => 'Inisiasi pembayaran sukses.',
                'order_id' => $order->id,
                'snap_token' => $snapToken,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment initiation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menginisiasi pembayaran.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Menangani notifikasi Webhook dari Payment Gateway (Midtrans).
     * Endpoint: POST /api/payments/webhook
     * Perhatian: Endpoint ini harus dipastikan bekerja di Railway!
     */
    public function handleWebhook(Request $request)
    {
        // Log request masuk untuk debugging di Railway
        Log::info('Midtrans Webhook Received', $request->all());

        try {
            // 1. Ambil Data Notifikasi dari Midtrans
            $notification = new Notification();
            $transaction = $notification->transaction_status;
            $paymentType = $notification->payment_type;
            $orderIdMidtrans = $notification->order_id; // Ini adalah order_id-timestamp

            // Pisahkan Order ID murni
            $parts = explode('-', $orderIdMidtrans);
            $orderId = (int)$parts[0]; 
            
            // 2. Cari Order di database
            $order = Order::findOrFail($orderId);
            $orderStatus = ''; // Status yang akan dicatat di tabel Order
            $paymentStatus = ''; // Status yang akan dicatat di tabel Payment

            // 3. Tentukan Status Transaksi
            if ($transaction == 'capture' || $transaction == 'settlement') {
                // Pembayaran sukses dan sudah masuk
                $orderStatus = 'Paid';
                $paymentStatus = 'settlement';
            } elseif ($transaction == 'pending') {
                $orderStatus = 'Processing'; // Atau tetap Pending
                $paymentStatus = 'pending';
            } elseif ($transaction == 'deny' || $transaction == 'cancel' || $transaction == 'expire') {
                // Pembayaran gagal/dibatalkan/kadaluarsa
                $orderStatus = 'Cancelled';
                $paymentStatus = $transaction;
            } else {
                return response()->json(['message' => 'Status notifikasi tidak dikenal.'], 400);
            }

            // 4. Proses Update Database menggunakan Transaksi
            DB::transaction(function () use ($order, $orderIdMidtrans, $paymentStatus, $orderStatus, $paymentType, $notification) {
                // 4a. Update Status Order
                if ($order->status != 'Paid' && $orderStatus == 'Paid') {
                    $order->update(['status' => $orderStatus]);
                } elseif ($order->status == 'Pending' && $orderStatus == 'Cancelled') {
                    $order->update(['status' => $orderStatus]);
                }
                
                // 4b. Catat Transaksi Pembayaran
                Payment::updateOrCreate(
                    ['transaction_id' => $orderIdMidtrans], // Cari berdasarkan ID Midtrans
                    [
                        'order_id' => $order->id,
                        'gross_amount' => $order->total_amount,
                        'payment_type' => $paymentType,
                        'status' => $paymentStatus,
                        'raw_response' => $notification->getJson(),
                    ]
                );
            });
            
            // 5. Kirim Respon Sukses ke Midtrans (Midtrans butuh respon 200 OK)
            return response()->json(['message' => 'Webhook berhasil diproses'], 200);

        } catch (\Exception $e) {
            // Log error dan kirim status 500 jika ada kegagalan internal
            Log::error('Webhook processing failed: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memproses webhook internal.'], 500);
        }
    }
}