@extends('admin.layouts.app') 

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Daftar Pesanan Pelanggan</h1>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <p class="text-sm text-gray-600">Total {{ $orders->total() }} Pesanan</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($orders as $order)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $order->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->user->name ?? 'User Dihapus' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">
                            Rp{{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($order->status == 'Paid')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $order->status }}
                                </span>
                            @elseif ($order->status == 'Pending')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $order->status }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $order->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d M Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 transition duration-150">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if ($orders->isEmpty())
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada pesanan yang ditemukan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection