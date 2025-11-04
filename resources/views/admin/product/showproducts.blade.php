@extends('admin/layout')

@section('title', 'ShowProduct Page')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ShowProduct</title>
</head>
<body>
    <div class="p-6 bg-white rounded-lg shadow-md max-w-2xl mx-auto">
        <a href="{{ route('products.index') }}" class="text-blue-500 hover:underline mb-4 inline-block">
            ← Back to Products
        </a>
    
        <div class="flex flex-col items-center">
            <!-- Product Image -->
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-64 h-64 object-cover rounded-lg mb-4">
            @else
                <div class="w-64 h-64 bg-gray-200 flex items-center justify-center rounded-lg mb-4">
                    <span class="text-gray-500">No Image</span>
                </div>
            @endif
    
            <!-- Product Details -->
            <h2 class="text-2xl font-bold">{{ $product->name }}</h2>
            <p class="text-gray-600 text-lg mt-2">Price: <span class="font-semibold">₱{{ number_format($product->price, 2) }}</span></p>
            <p class="text-gray-500 mt-2">{{ $product->description }}</p>
    
            <!-- Category -->
            <p class="text-sm text-gray-600 mt-2">
                Category: <span class="font-semibold">{{ $product->category->name ?? 'Uncategorized' }}</span>
            </p>

            <!-- Stock Quantity -->
            <p class="text-sm text-gray-600 mt-2">
                Stock: <span class="font-semibold">{{ $product->stock_quantity }}</span>
            </p>
    
            <!-- Action Buttons -->
            <div class="mt-4 flex gap-3">
                <a href="{{ route('products.edit', $product->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                    ✏️ Edit
                </a>
                <form action="{{ route('products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">
                        🗑 Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
@endsection
