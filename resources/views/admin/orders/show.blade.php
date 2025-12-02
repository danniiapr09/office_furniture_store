@extends('layouts.app') {{-- Ganti ini jika Anda menggunakan layout yang berbeda, misalnya 'layouts.admin' --}}

@section('content')

<div class="container mx-auto p-4">

    {{-- Header Halaman --}}
    <h1 class="text-2xl font-bold mb-4">Detail Order #{{ $order->id }}</h1>

    {{-- Informasi Dasar Order --}}
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold border-b pb-2 mb-4">Ringkasan</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="font-medium">Tanggal Order:</p>
                <p>{{ $order->created_at->format('d M Y H:i') }}</p>
            </div>
            <div>
                <p class="font-medium">Status:</p>
                <p class="text-green-600">{{ $order->status }}</p>
            </div>
            <div>
                <p class="font-medium">Customer:</p>
                <p>{{ $order->user->name }}</p>
            </div>
            <div>
                <p class="font-medium">Total Harga:</p>
                <p>Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- Detail Item yang Dipesan --}}
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold border-b pb-2 mb-4">Item Order</h2>
        <ul class="space-y-4">
            @foreach ($order->items as $item)
                <li class="border-b pb-2">
                    <p class="font-medium">{{ $item->furniture->name }} ({{ $item->quantity }}x)</p>
                    <p class="text-gray-600">Harga Satuan: Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                </li>
            @endforeach
        </ul>
    </div>

</div>

@endsection