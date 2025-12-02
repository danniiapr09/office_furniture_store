@extends('layouts.app') {{-- KRITIS: Ganti 'layouts.app' jika layout utama Anda bernama lain (misalnya 'layouts.admin') --}}

@section('content')

<div class="container mx-auto p-8 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">

        {{-- Header Halaman --}}
        <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-2">
            Detail Order #{{ $order->id }}
        </h1>

        {{-- RINGKASAN ORDER --}}
        <div class="bg-white shadow-xl rounded-xl p-6 mb-8 border border-gray-200">
            <h2 class="text-xl font-bold text-indigo-600 mb-4">Informasi Transaksi</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="space-y-1">
                    <p class="font-semibold text-gray-700">Customer:</p>
                    <p class="text-gray-500">{{ $order->user->name ?? 'User Dihapus' }}</p>
                </div>
                <div class="space-y-1">
                    <p class="font-semibold text-gray-700">Tanggal Order:</p>
                    <p class="text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="font-semibold text-gray-700">Status Pembayaran:</p>
                    {{-- Asumsi status ada di kolom 'status' pada relasi payments --}}
                    @if ($order->payments->isNotEmpty())
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $order->payments->first()->status ?? 'N/A' }}
                        </span>
                    @else
                         <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Menunggu Pembayaran
                        </span>
                    @endif
                </div>
                <div class="space-y-1">
                    <p class="font-semibold text-gray-700">Total Akhir:</p>
                    <p class="text-2xl font-extrabold text-indigo-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- DETAIL ITEM --}}
        <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Item yang Dipesan</h2>
            
            <ul class="divide-y divide-gray-200">
                @foreach ($order->items as $item)
                    <li class="flex justify-between items-center py-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-medium text-gray-900">{{ $item->furniture->name ?? 'Furniture Dihapus' }}</p>
                            <p class="text-sm text-gray-500">Kuantitas: {{ $item->quantity }}x</p>
                        </div>
                        <div class="text-right">
                            <p class="text-base font-semibold text-gray-800">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-400">Harga Satuan</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        
    </div>
</div>

@endsection