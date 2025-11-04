@extends('superadmin.layout')
@section('title', 'Manage Products')
@section('content')

<div class="max-w-6xl mx-auto py-10 px-4">
      <h1 class="text-3xl font-bold text-green-800 mb-8 text-center flex items-center justify-center gap-2">
                    <i class="bi bi-basket-fill"></i> Manage Products
    </h1>
    <form id="filterForm" class="mb-6 flex flex-wrap gap-2 items-end" autocomplete="off">
        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Search products..." class="border px-4 py-2 rounded w-64">

        {{-- Product Type Filter --}}
        <select name="type" id="typeSelect" class="border px-4 py-2 rounded">
            <option value="">All Types</option>
            @foreach($types as $type)
                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>

        {{-- Unit Filter --}}
        <select name="unit" id="unitSelect" class="border px-4 py-2 rounded">
            <option value="">All Units</option>
            @foreach($units as $unit)
                <option value="{{ $unit }}" {{ request('unit') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
            @endforeach
        </select>

        {{-- Price Filter --}}
        <input type="number" name="min_price" id="minPriceInput" value="{{ request('min_price') }}" placeholder="Min Price" class="border px-2 py-2 rounded w-24">
        <input type="number" name="max_price" id="maxPriceInput" value="{{ request('max_price') }}" placeholder="Max Price" class="border px-2 py-2 rounded w-24">
        <a href="?blocklist=1" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold shadow transition flex items-center gap-2">
            <i class="bi bi-archive"></i> Blocklist
        </a>
        {{-- <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded">Filter</button> --}}
    </form>
    <div id="productsTable">
        <table class="min-w-full bg-white rounded-xl shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2">Img</th>
                    <th class="px-4 py-2">Product ID</th>
                    <th class="px-4 py-2">Product Name</th>
                    <th class="px-4 py-2">Product Type</th>
                    <th class="px-4 py-2">Unit</th>
                    <th class="px-4 py-2">Price(₱)</th>
                    <!-- Removed Weight(kg) column -->
                    <th class="px-4 py-2">Stock</th>
                    <th class="px-4 py-2">Farm Owner</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td class="px-4 py-2">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('agri-product.png') }}"
                             alt="Product Image" class="w-12 h-12 object-cover rounded border">
                    </td>
                    <td class="px-4 py-2">{{ $product->id }}</td>
                    <td class="px-4 py-2">{{ $product->name }}</td>
                    <td class="px-4 py-2">{{ $product->type ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $product->unit ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ number_format($product->price, 2) }}</td>
                    <!-- Removed Weight(kg) cell -->
                    <td class="px-4 py-2">{{ $product->stock_quantity ?? 'N/A' }}</td>
                    <td class="px-4 py-2">
    {{ $product->admin ? $product->admin->name : 'N/A' }}
</td>
                    <td class="px-4 py-2 flex gap-2 justify-center">
                        @if(!$product->blocklisted)
                        <form action="{{ route('superadmin.products.blocklist', $product->id) }}" method="POST" onsubmit="return confirm('Blocklist this product?')" class="inline">
                            @csrf
                            <button type="submit" class="text-red-600 bg-red-100 px-2 py-1 rounded font-semibold" title="Block">
                                Block
                            </button>
                        </form>
                        @else
                        <form action="{{ route('superadmin.products.unblocklist', $product->id) }}" method="POST" onsubmit="return confirm('Remove from blocklist?')" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 bg-green-100 px-2 py-1 rounded font-semibold" title="Unblocklist">
                                Unblocklist
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-6 text-gray-500">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-6 flex justify-center">
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const productsTable = document.getElementById('productsTable');
    const searchInput = document.getElementById('searchInput');
    const typeSelect = document.getElementById('typeSelect');
    const unitSelect = document.getElementById('unitSelect');
    const minPriceInput = document.getElementById('minPriceInput');
    const maxPriceInput = document.getElementById('maxPriceInput');
    let timeout = null;

    function fetchProducts() {
        const params = new URLSearchParams(new FormData(filterForm)).toString();
        fetch(`{{ route('superadmin.products') }}?${params}`)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('productsTable').innerHTML;
                productsTable.innerHTML = newTable;
            });
    }

    [searchInput, typeSelect, unitSelect, minPriceInput, maxPriceInput].forEach(el => {
        el.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(fetchProducts, 300); // debounce for better UX
        });
        el.addEventListener('change', fetchProducts);
    });
});
</script>
@endsection