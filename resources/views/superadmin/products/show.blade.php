@extends('superadmin.layout')
@section('title', 'Product Details')
@section('content')
<div class="max-w-xl mx-auto py-10 px-4">
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-2xl font-bold text-green-800 mb-4">{{ $product->name }}</h2>
        <p><strong>Category:</strong> {{ $product->category }}</p>
        <p><strong>Owner:</strong> {{ $product->owner->name ?? '' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($product->status) }}</p>
        <p><strong>Description:</strong> {{ $product->description }}</p>
        <div class="mt-4">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="w-40 h-40 object-cover rounded-lg border" alt="Product Image">
            @endif
        </div>
        <a href="{{ route('superadmin.products.index') }}" class="mt-6 inline-block bg-green-700 text-white px-4 py-2 rounded">Back to Products</a>
    </div>
</div>
@endsection