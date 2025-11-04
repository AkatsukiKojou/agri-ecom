{{-- filepath: resources/views/user/products/index.blade.php --}}
@extends('user.layout')

@section('content')
@php use Illuminate\Support\Str; @endphp

    <div class="container mx-auto px-2 pt-0.5 pb-6 bg-white"
     x-data="productListComponent()">

    <!-- Header Controls (Scrollable, not fixed) -->
<div class="w-full bg-white p-3">

    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Filters (Left) -->
    <div class="flex flex-col md:flex-row gap-4 w-full md:w-1/6 max-w-xs">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </span>
                <select id="sortBy" x-model="sortBy"
                    class="w-full border border-green-300 rounded-lg p-3 pl-10 focus:outline-none focus:ring-2 focus:ring-green-600 shadow text-sm">
                    <option value="none">Sort: Default</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                </select>
            </div>
        </div>

        <!-- Search (Center) -->
        <div class="flex justify-center md:w-1/3">
            <div class="relative w-full max-w-xl">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                </span>
                <input 
                    type="text" 
                    id="searchInput" 
                    name="search" 
                    x-model="search"
                    placeholder="Search by Products Name, Type, or Location..." 
                    class="w-full border border-green-300 rounded-lg p-3 pl-10 focus:outline-none focus:ring-2 focus:ring-green-600 shadow text-sm text-center" 
                />
            </div>
        </div>

        <!-- Cart & Purchase (Right) -->
        <div class="flex space-x-4 items-center justify-center md:justify-end">
            <!-- Cart -->
            <a href="{{ route('cart.index') }}"
                class="relative text-green-800 font-medium hover:underline hover:text-green-600 flex items-center space-x-2 transition">
                <svg id="cart-icon" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.3 5.2a1 1 0 001 .8h11.6a1 1 0 001-.8L17 13M9 21h.01M15 21h.01"/>
                </svg>
                <template x-if="cartCount > 0">
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full"
                        x-text="cartCount"></span>
                </template>
                <span class="ml-1">Cart</span>
            </a>

            <!-- Purchase -->
            <a href="{{ route('user.orders') }}"
                class="relative flex items-center text-green-800 font-medium hover:text-green-600 hover:underline transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M9 5h6M9 3h6a2 2 0 012 2v2H7V5a2 2 0 012-2zM7 9h10M7 13h10M7 17h10M5 7h14a2 2 0 012 2v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9a2 2 0 012-2z" />
                </svg>
                @php $purchaseCount = auth()->user()->orders()->count(); @endphp
                @if ($purchaseCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                        {{ $purchaseCount }}
                    </span>
                @endif
                <span>Purchase</span>
            </a>
        </div>
        
    </div>
</div>


<!-- Category Filter -->
<div class="flex flex-wrap justify-center gap-2 md:gap-4 mb-6 mt-6">
    <button
        @click="selectedType = ''"
        :class="selectedType === '' ? 'bg-green-700 text-white' : 'bg-green-100 text-green-800'"
        class="px-4 py-2 rounded-full font-semibold shadow hover:bg-green-200 transition border border-green-300 focus:outline-none"
    >
        All
    </button>
    <template x-for="cat in ['Fertilizer', 'Vegetables', 'Fruits', 'Seeds', 'Tools', 'Pesticides']" :key="cat">
        <button
            @click="selectedType = cat"
            :class="selectedType === cat ? 'bg-green-700 text-white' : 'bg-green-100 text-green-800'"
            class="px-4 py-2 rounded-full font-semibold shadow hover:bg-green-200 transition border border-green-300 focus:outline-none"
        >
            <span x-text="cat"></span>
        </button>
    </template>
 
</div>

<!-- Add top padding to avoid content overlap -->
<div class="pt-4"></div>
<h1 class="text-4xl font-extrabold text-center mb-10 text-green-900 tracking-tight drop-shadow-lg">Available Products</h1>

    <div class="text-center text-gray-500 mt-6 text-sm"
         x-show="!sortedProducts().some(p => productMatches(p.name, p.description, p.admin_name, search)) && search.length > 0">
        No products found.
    </div>

    <!-- Flying Image Animation -->
<div x-show="fly"
     x-transition
     :style="flyStyle"
     x-cloak>
    <img :src="flySrc" class="fly-img" />
</div>

    <!-- Product Grid -->
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6 gap-4 mt-6">
    <template x-for="product in filteredProducts()" :key="product.id">
        <div
             class="product-card border border-lime-200 rounded-xl p-2 shadow-md hover:shadow-xl transition duration-300 bg-white/80 flex flex-col justify-between h-full">

            <a :href="'{{ route('product.show', '') }}/' + product.id">
                <img :src="'{{ asset('storage/') }}/' + product.image" :alt="product.name" class="w-full h-28 object-cover rounded-md mb-2 shadow">
                <h2 class="text-base font-bold text-green-900 capitalize truncate" x-text="product.name"></h2>
                <p class="text-xs text-gray-700 mb-1" x-text="truncateDescription(product.description, 10)"></p>
                <p class="text-green-700 font-bold mb-1 text-sm">
                    ₱<span x-text="product.price"></span> 
                    <span class="text-gray-700"> / <span x-text="product.unit"></span></span>
                </p>
                <p class="text-xs text-gray-500 mb-1">Stock: <span x-text="product.stock_quantity"></span></p>
                <div class="flex items-center space-x-2 mt-1">
                <img :src="product.profile_photo ? '{{ asset('storage/') }}/' + product.profile_photo : '{{ asset('default-profile.png') }}'"
                    class="w-5 h-5 rounded-full object-cover"
                    :alt="product.farm_owner ? product.farm_owner : 'Unknown'"
                    onerror="this.onerror=null;this.src='{{ asset('default-profile.png') }}';" />
                <span class="text-xs text-gray-500 truncate">
                    <span class="font-medium text-gray-700" x-text="product.farm_owner ? product.farm_owner : 'Unknown'"></span>
                </span>
                </div>
            </a>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-1 mt-2">
                <template x-if="product.stock_quantity > 0">
                    <div>
                        <button @click="addToCart(product, $event)"
                            class="w-full bg-lime-600 hover:bg-lime-700 text-white font-semibold py-1.5 rounded-lg shadow transition text-sm flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.3 5.2a1 1 0 001 .8h11.6a1 1 0 001-.8L17 13M9 21h.01M15 21h.01"/>
                            </svg>
                            Add to Cart
                        </button>
                        <form :action="'{{ route('buy.now', '') }}/' + product.id" method="POST" class="w-full mt-1">
                            @csrf
                            <button type="submit"
                                class="w-full bg-green-800 hover:bg-green-900 text-white font-semibold py-1.5 rounded-lg shadow transition text-sm">
                                Buy Now
                            </button>
                        </form>
                    </div>
                </template>
                <template x-if="product.stock_quantity == 0">
                    <button type="button"
                        class="w-full bg-gray-400 text-white font-semibold py-1.5 rounded-lg shadow transition text-sm cursor-not-allowed"
                        disabled>
                        Out of Stock
                    </button>
                </template>
            </div>
        </div>
    </template>
</div>
</div>
<script>
    function productListComponent() {
        return {
            search: '',
            sortBy: 'none',
            selectedType: '',
            products: {{ Js::from($products->map(fn($p) => [
                'id' => $p->id,
                'name' => strtolower($p->name),
                'description' => strtolower($p->description),
                'price' => $p->price,
                'image' => $p->image,
                'admin_name' => $p->admin ? strtolower($p->admin->name) : '',
                'admin_image' => $p->admin ? ($p->admin->profile_image ?? '') : '',
                'farm_owner' => $p->admin && $p->admin->profile ? $p->admin->profile->farm_owner : null,
                'farm_name' => $p->admin && $p->admin->profile ? $p->admin->profile->farm_name : null,
                'profile_photo' => $p->admin && $p->admin->profile ? $p->admin->profile->profile_photo : null,
                'barangay' => $p->admin && $p->admin->profile ? strtolower($p->admin->profile->barangay ?? '') : '',
                'city' => $p->admin && $p->admin->profile ? strtolower($p->admin->profile->city ?? '') : '',
                'province' => $p->admin && $p->admin->profile ? strtolower($p->admin->profile->province ?? '') : '',
                'region' => $p->admin && $p->admin->profile ? strtolower($p->admin->profile->region ?? '') : '',
                'stock_quantity' => $p->stock_quantity,
                'unit' => $p->unit,
                'type' => $p->type ?? ''
            ])) }},
            cartCount: {{ session('cart') ? count(session('cart')) : 0 }},
            fly: false,
            flySrc: '',
            flyStyle: '',
            sortedProducts() {
                let sorted = [...this.products];
                if (this.sortBy === 'price_asc') sorted.sort((a, b) => a.price - b.price);
                else if (this.sortBy === 'price_desc') sorted.sort((a, b) => b.price - a.price);
                return sorted;
            },
            filteredProducts() {
                return this.sortedProducts().filter(p => (
                    (this.selectedType === '' || p.type === this.selectedType)
                    && this.productMatches(p, this.search)
                ));
            },
            productMatches(p, search) {
                search = search.toLowerCase();
                return p.name.includes(search)
                    || p.description.includes(search)
                    || p.admin_name.includes(search)
                    || (p.farm_owner && p.farm_owner.toLowerCase().includes(search))
                    || (p.farm_name && p.farm_name.toLowerCase().includes(search))
                    || (p.unit && p.unit.toLowerCase().includes(search))
                    || (p.barangay && p.barangay.includes(search))
                    || (p.city && p.city.includes(search))
                    || (p.province && p.province.includes(search))
                    || (p.region && p.region.includes(search));
            },
            truncateDescription(desc, wordLimit) {
                const words = desc.split(' ');
                return words.length > wordLimit ? words.slice(0, wordLimit).join(' ') + '...' : desc;
            },
            addToCart(product, event) {
                const img = event.target.closest('.product-card').querySelector('img');
                const cartIcon = document.getElementById('cart-icon');
                const imgRect = img.getBoundingClientRect();
                const cartRect = cartIcon.getBoundingClientRect();

                this.flySrc = img.src;
                this.flyStyle = `
                    left:${imgRect.left}px;
                    top:${imgRect.top}px;
                    width:${imgRect.width}px;
                    height:${imgRect.height}px;
                `;
                this.fly = true;

                this.$nextTick(() => {
                    const flyImg = document.querySelector('.fly-img');
                    if (flyImg) {
                        // Move to cart position with scale and curve
                        flyImg.style.left = imgRect.left + 'px';
                        flyImg.style.top = imgRect.top + 'px';
                        flyImg.style.width = imgRect.width + 'px';
                        flyImg.style.height = imgRect.height + 'px';

                        setTimeout(() => {
                            flyImg.classList.add('fly-active');
                            flyImg.style.left = cartRect.left + 'px';
                            flyImg.style.top = (cartRect.top - 40) + 'px';
                        }, 50);

                        setTimeout(() => {
                            this.fly = false;
                            flyImg.classList.remove('fly-active');
                        }, 800);
                    }
                });

                // Ajax request to add to cart
                fetch(`/cart/add/${product.id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.cartCount++;
                        cartIcon.classList.add('animate-bounce');
                        setTimeout(() => cartIcon.classList.remove('animate-bounce'), 400);
                    }
                });
            }
        }
    }
</script>
<style>
.fly-img {
    position: fixed;
    z-index: 1000;
    border-radius: 50%;
    box-shadow: 0 8px 24px rgba(76,175,80,0.25);
    pointer-events: none;
    transition:
        transform 0.7s cubic-bezier(.22,1,.36,1),
        opacity 0.7s,
        box-shadow 0.7s;
    opacity: 1;
}
.fly-img.fly-active {
    transform: scale(0.3) translateY(-120px) rotate(-25deg);
    opacity: 0;
    box-shadow: 0 2px 8px rgba(76,175,80,0.12);
}
</style>
@endsection
