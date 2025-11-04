{{-- @extends('user.layout')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
	<!-- Search Bar -->
	<div class="mb-6 flex justify-center">
		<form action="{{ route('user.dashboard') }}" method="GET" class="w-full max-w-2xl flex">
			<input type="text" name="search" class="flex-1 px-4 py-2 border border-green-300 rounded-l-full focus:ring-2 focus:ring-green-400 bg-white" placeholder="Search products, services, users, events..." value="{{ request('search') }}">
			<button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 rounded-r-full">Search</button>
		</form>
	</div>
	<!-- 3 Column Layout -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 h-[80vh]">
    <!-- Left: Top Products & Top Services -->
    <div class="space-y-6 col-span-1 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl shadow-lg p-4 overflow-y-auto h-full">
        <h3 class="font-bold text-green-700 mb-4 flex items-center gap-3 justify-center text-xl"><i class="bi bi-basket-fill text-green-600 text-2xl"></i> Products</h3>
        <div class="grid grid-cols-1 gap-4">
            @forelse($randomProducts as $product)
                <div class="bg-white rounded-xl p-4 flex items-center gap-3 shadow hover:shadow-md transition">
                    @if($product->image)
                        <img src="{{ asset('storage/' . ltrim($product->image, '/')) }}" alt="Product Image" class="w-8 h-8 rounded object-cover">
                    @else
                        <img src="{{ asset('/storage/default-product.png') }}" alt="Default Product" class="w-8 h-8 rounded object-cover">
                    @endif
                    <span class="font-medium ml-2">{{ $product->product_name ?? $product->name }}</span>
                </div>
            @empty
                <div class="text-gray-400 text-sm">No products yet.</div>
            @endforelse
        </div>
        <h3 class="font-bold text-green-700 mb-4 flex items-center gap-3 justify-center text-xl">
            <i class="bi bi-gear-wide-connected text-green-600 text-2xl"></i> Top Services
        </h3>
        <div class="grid grid-cols-1 gap-4">
            @forelse($services->take(3) as $service)
                <div class="bg-white rounded-xl p-4 flex items-center gap-3 shadow hover:shadow-md transition">
                    @if($service->images)
                        <img src="{{ asset('storage/' . ltrim($service->images, '/')) }}" alt="Service Image" class="w-8 h-8 rounded object-cover">
                    @else
                        <img src="{{ asset('/storage/default-service.png') }}" alt="Default Service" class="w-8 h-8 rounded object-cover">
                    @endif
                    <span class="font-medium ml-2">{{ $service->service_name }}</span>
                </div>
            @empty
                <div class="text-gray-400 text-sm">No services yet.</div>
            @endforelse
        </div>
    </div>
    <!-- Center: Events -->
    <div class="col-span-1 md:col-span-2 bg-gradient-to-br from-white to-green-50 rounded-2xl shadow-lg p-4 overflow-y-auto h-full">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h3 class="font-bold text-green-700 mb-6 flex items-center gap-3 text-xl"><i class="bi bi-calendar-event text-green-600 text-2xl"></i> Latest Events</h3>
            @php($events = \App\Models\Event::latest()->take(20)->get())
            @php($grouped = $events->groupBy('created_by'))
            @forelse($grouped as $adminId => $adminEvents)
                @php($admin = \App\Models\User::find($adminId))
                <div class="mb-10 bg-green-50 rounded-xl p-4 shadow hover:shadow-md transition">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ $admin && $admin->profile && $admin->profile->profile_photo ? asset('storage/' . ltrim($admin->profile->profile_photo, '/')) : asset('/storage/default.png') }}" class="w-10 h-10 rounded-full object-cover border" alt="Admin Photo">
                        <span class="font-semibold text-green-700 text-lg">{{ $admin ? $admin->name : 'Admin' }}</span>
                    </div>
                    @foreach($adminEvents as $event)
                        <div class="mb-8">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs text-gray-500">{{ $event->created_at->format('F j, Y g:i A') }}</span>
                            </div>
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" class="w-full h-48 object-cover rounded mb-2" />
                            @endif
                            <div class="text-base mb-1">{{ $event->description }}</div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="text-gray-400 text-sm">No events yet.</div>
            @endforelse
        </div>
    </div>
    <!-- Right: Top Likes & Followers -->
    <div class="space-y-6 col-span-1 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl shadow-lg p-4 overflow-y-auto h-full">
        <h3 class="font-bold text-green-700 mb-4 flex items-center gap-3 justify-center text-xl"><i class="bi bi-hand-thumbs-up-fill text-green-600 text-2xl"></i> Top Likes</h3>
        @php($topLikedUsers = \App\Models\User::withCount('profileLikes')->where('role', 'admin')->orderByDesc('profile_likes_count')->take(3)->get())
        @forelse($topLikedUsers as $user)
            <div class="bg-white rounded-xl p-4 mb-4 flex items-center gap-3 shadow hover:shadow-md transition">
                @if($user->profile && $user->profile->profile_photo)
                    <img src="{{ asset('storage/' . ltrim($user->profile->profile_photo, '/')) }}" alt="Admin Photo" class="w-8 h-8 rounded-full object-cover border">
                @else
                    <img src="{{ asset('/storage/default.png') }}" alt="Default Photo" class="w-8 h-8 rounded-full object-cover border">
                @endif
                <span class="font-medium">{{ $user->name }}</span>
            </div>
        @empty
            <div class="text-gray-400 text-sm">No likes yet.</div>
        @endforelse
        <h3 class="font-bold text-green-700 mb-4 flex items-center gap-3 justify-center text-xl"><i class="bi bi-people-fill text-green-600 text-2xl"></i> Top Followers</h3>
        @php($topFollowers = \App\Models\User::withCount('profileFollowers')->where('role', 'admin')->orderByDesc('profile_followers_count')->take(3)->get())
        @forelse($topFollowers as $user)
            <div class="bg-white rounded-xl p-4 mb-4 flex items-center gap-3 shadow hover:shadow-md transition">
                @if($user->profile && $user->profile->profile_photo)
                    <img src="{{ asset('storage/' . ltrim($user->profile->profile_photo, '/')) }}" alt="Admin Photo" class="w-8 h-8 rounded-full object-cover border">
                @else
                    <img src="{{ asset('/storage/default.png') }}" alt="Default Photo" class="w-8 h-8 rounded-full object-cover border">
                @endif
                <span class="font-medium text-center">{{ $user->name }}</span>
            </div>
        @empty
            <div class="text-gray-400 text-sm text-center">No followers yet.</div>
        @endforelse
    </div>
</div>
@endsection --}}
