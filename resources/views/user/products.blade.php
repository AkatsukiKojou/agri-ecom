{{-- @extends('user.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Products</h1>

        <!-- Search Form -->
        <form action="{{ route('user.products.index') }}" method="GET" class="flex items-center space-x-4">
            <input type="text" name="search" placeholder="Search by name or category" value="{{ request('search') }}" class="px-4 py-2 border border-gray-300 rounded-lg">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Search</button>
        </form>

        <a href="{{ route('cart.index') }}" class="flex items-center py-2 px-4 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13m-11.5 0a1.5 1.5 0 103 0m7.5 0a1.5 1.5 0 103 0" />
            </svg>
            Cart
        </a>
        <a href="{{ route('user.orders.index') }}" class="block py-2">🛍️ My Orders</a>
    </div>

    <!-- Show error message if no products are available -->
    @if($products->isEmpty())
        <div class="text-center py-4 bg-red-100 text-red-700 border border-red-300 rounded">
            No products available matching your search criteria.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden transition-transform hover:scale-105 duration-300">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                    @endif

                    <div class="p-5">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h2>
                        <p class="text-gray-600 text-sm mb-2">{{ $product->description }}</p>
                        <p class="text-gray-800 text-sm mb-1"><strong>Price:</strong> ${{ $product->price }}</p>
                        <p class="text-gray-800 text-sm mb-1"><strong>Stock:</strong> {{ $product->stock_quantity }} available</p>
                        <p class="text-gray-800 text-sm mb-4"><strong>Category:</strong> {{ $product->category }}</p>

                        <div class="flex gap-3">
                            <!-- Add to Cart -->
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit">Add to Cart</button>
                            </form>

                            <!-- Buy Now Button -->
                            <form action="{{ route('buy-now.show', $product->id) }}" method="GET">
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">Buy Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Pagination -->
    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection --}}
