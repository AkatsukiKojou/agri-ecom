{{-- filepath: resources/views/admin/product/archived.blade.php --}}
@extends('admin.layout')

@section('title', 'Archived Products')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white shadow-md rounded-lg">
    <h2 class="text-2xl font-semibold mb-6">Archived Products</h2>
    <a href="{{ route('products.index') }}" class="text-blue-500 hover:underline mb-4 inline-block">
        ← Back to Active Products
    </a>

    {{-- Search Bar --}}
    <form method="GET" action="{{ route('products.archived.index') }}" class="mb-4 flex items-center gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
            class="border border-gray-300 rounded px-3 py-2 w-64 focus:ring-2 focus:ring-yellow-400"
            placeholder="Search archived products...">
        <button type="submit"
            class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 transition font-semibold text-sm">
            <i class="bi bi-search"></i> Search
        </button>
    </form>

    @if(session('message'))
        <div class="p-4 bg-green-100 text-green-800 mb-4 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if($archived->isEmpty())
        <div class="text-gray-500 text-center py-10">No archived products found.</div>
    @else
    <div class="overflow-x-auto rounded-xl shadow mt-4">
        <table class="w-full table-auto border rounded-xl overflow-hidden text-xs" id="archivedProductsTable">
            <thead>
                <tr class="bg-gradient-to-r from-yellow-600 to-yellow-400 text-white uppercase tracking-wide">
                    <th class="px-2 py-2 border text-center">Img</th>
                    <th class="px-2 py-2 border">Product Name</th>
                    <th class="px-2 py-2 border">Product Type</th>
                    <th class="px-2 py-2 border">Unit</th>
                    <th class="px-2 py-2 border">Price(₱)</th>
                    <th class="px-2 py-2 border">Stock</th>
                    <th class="px-2 py-2 border">Description</th>
                    <th class="px-2 py-2 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($archived as $product)
                <tr class="even:bg-gray-50 hover:bg-yellow-50 transition">
                    <td class="px-2 py-1 border text-center">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-8 h-8 object-cover rounded shadow mx-auto">
                        @else
                            <span class="text-gray-400 italic">No Img</span>
                        @endif
                    </td>
                    <td class="px-2 py-1 border font-semibold text-gray-800 truncate max-w-[90px]">{{ $product->name }}</td>
                    <td class="px-2 py-1 border text-gray-600 truncate max-w-[70px]">{{ $product->type ?? 'N/A' }}</td>
                    <td class="px-2 py-1 border text-gray-600">{{ ucfirst($product->unit) }}</td>
                    <td class="px-2 py-1 border text-yellow-700 font-bold">₱{{ number_format($product->price, 2) }}</td>
                    <td class="px-2 py-1 border">
                        <span class="inline-block bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold text-xs shadow">{{ $product->stock_quantity }}</span>
                    </td>
                    <td class="px-2 py-1 border text-gray-600 truncate max-w-[80px]" title="{{ $product->description }}">
                        {{ Str::limit($product->description, 10 ) }}
                    </td>
                    <td class="px-2 py-1 border text-center">
                        <form action="{{ route('products.restore', $product->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 text-xs" title="Restore">
                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                            </button>
                        </form>
                        <form action="{{ route('products.forceDelete', $product->id) }}" method="POST" class="inline ml-1" onsubmit="return confirm('Permanently delete this product? This cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 text-xs" title="Delete Permanently">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection