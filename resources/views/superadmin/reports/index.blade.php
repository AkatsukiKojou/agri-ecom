@extends('superadmin.layout')
@section('title', 'Reports & Analytics')
@section('content')
<div class="max-w-6xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-green-800 mb-8 flex items-center gap-2">
        <i class="bi bi-bar-chart-line-fill"></i> Reports & Analytics
    </h1>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-green-100 rounded-xl p-6 flex flex-col items-center shadow">
            <span class="font-semibold text-green-900 mb-2">Total Users</span>
            <span class="text-3xl font-extrabold text-green-700">{{ $totalUsers }}</span>
        </div>
        <div class="bg-green-100 rounded-xl p-6 flex flex-col items-center shadow">
            <span class="font-semibold text-green-900 mb-2">LSA</span>
            <span class="text-3xl font-extrabold text-green-700">{{ $totalAdmins }}</span>
        </div>

        <div class="bg-green-100 rounded-xl p-6 flex flex-col items-center shadow">
            <span class="font-semibold text-green-900 mb-2">Buyers</span>
            <span class="text-3xl font-extrabold text-green-700">{{ $totalBuyers }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-lime-100 rounded-xl p-6 flex flex-col items-center shadow">
            <span class="font-semibold text-green-900 mb-2">Total Products</span>
            <span class="text-3xl font-extrabold text-green-700">{{ $totalProducts }}</span>
        </div>
        <div class="bg-lime-100 rounded-xl p-6 flex flex-col items-center shadow">
            <span class="font-semibold text-green-900 mb-2">Out of Stock</span>
            <span class="text-3xl font-extrabold text-red-600">{{ $outOfStock }}</span>
        </div>
        <div class="bg-lime-100 rounded-xl p-6 flex flex-col items-center shadow col-span-2">
            <span class="font-semibold text-green-900 mb-2">Products per Type</span>
            <ul>
                @foreach($productsPerType as $type => $total)
                    <li class="text-green-800">{{ $type }}: <span class="font-bold">{{ $total }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="bg-blue-100 rounded-xl p-6 flex flex-col items-center shadow col-span-2">
            <span class="font-semibold text-blue-900 mb-2">Total Services</span>
            <span class="text-3xl font-extrabold text-blue-700">{{ $totalServices }}</span>
        </div>
        <div class="bg-blue-100 rounded-xl p-6 flex flex-col items-center shadow col-span-2">
            <span class="font-semibold text-blue-900 mb-2">Services per Type</span>
            <ul>
                @foreach($servicesPerType as $type => $total)
                    <li class="text-blue-800">{{ $type }}: <span class="font-bold">{{ $total }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="bg-blue-100 rounded-xl p-6 flex flex-col items-center shadow col-span-2">
            <span class="font-semibold text-blue-900 mb-2">Unavailable Services</span>
            <span class="text-3xl font-extrabold text-red-600">{{ $unavailableServices }}</span>
        </div>
    <div class="mb-8">
        <h2 class="text-xl font-bold text-blue-800 mb-2">Top 5 Services (by Bookings)</h2>
        <table class="min-w-full bg-white rounded-xl shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2">Service Name</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Bookings</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topServices as $service)
                <tr>
                    <td class="px-4 py-2">{{ $service->service_name }}</td>
                    <td class="px-4 py-2">{{ $service->bookings_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-bold text-green-800 mb-2">Top 5 Products (by Stock)</h2>
        <table class="min-w-full bg-white rounded-xl shadow">
            <thead>
                <tr>
                    <th class="px-4 py-2">Product Name</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topProducts as $product)
                <tr>
                    <td class="px-4 py-2">{{ $product->name }}</td>
                    <td class="px-4 py-2">{{ $product->type }}</td>
                    <td class="px-4 py-2">{{ $product->stock_quantity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

 

    <div class="mb-8">
        <h2 class="text-xl font-bold text-green-800 mb-4 flex items-center gap-2">
            <i class="bi bi-trophy-fill text-yellow-500"></i> Top Admins/LSA - Most Products Sold
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($topSellers as $i => $admin)
            <div class="bg-gradient-to-br from-green-100 to-lime-100 rounded-xl shadow-lg p-6 flex items-center gap-4 border-2 {{ $i == 0 ? 'border-yellow-400' : 'border-green-200' }}">
                @if(!empty($admin->profile) && !empty($admin->profile->profile_photo))
                    <span class="inline-block rounded-full overflow-hidden w-12 h-12 border-2 border-green-700">
                        <img src="{{ asset('storage/' . $admin->profile->profile_photo) }}" alt="{{ $admin->name }}" class="object-cover w-full h-full" />
                    </span>
                @else
                    <span class="inline-block bg-green-700 rounded-full p-2">
                        <i class="bi bi-person-badge-fill text-white text-2xl"></i>
                    </span>
                @endif
                <div class="flex-1">
                    <div class="font-bold text-green-900 text-lg">{{ $admin->name }}</div>
                    <div class="text-green-700 text-sm">{{ $admin->email ?? '' }}</div>
                </div>
                <div class="flex flex-col items-end">
                    <span class="font-extrabold text-2xl text-green-700">{{ $admin->products_sold }}</span>
                    <span class="text-xs text-green-800">Products Sold</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="mb-8">
        <h2 class="text-xl font-bold text-green-800 mb-4 flex items-center gap-2">
            <i class="bi bi-star-fill text-lime-500"></i> Top Admins/LSA - Most Services Booked
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($topServiceBookers as $i => $admin)
            <div class="bg-gradient-to-br from-lime-100 to-green-100 rounded-xl shadow-lg p-6 flex items-center gap-4 border-2 {{ $i == 0 ? 'border-lime-400' : 'border-green-200' }}">
                @if(!empty($admin->profile) && !empty($admin->profile->profile_photo))
                    <span class="inline-block rounded-full overflow-hidden w-12 h-12 border-2 border-lime-600">
                        <img src="{{ asset('storage/' . $admin->profile->profile_photo) }}" alt="{{ $admin->name }}" class="object-cover w-full h-full" />
                    </span>
                @else
                    <span class="inline-block bg-lime-600 rounded-full p-2">
                        <i class="bi bi-person-badge-fill text-white text-2xl"></i>
                    </span>
                @endif
                <div class="flex-1">
                    <div class="font-bold text-green-900 text-lg">{{ $admin->name }}</div>
                    <div class="text-green-700 text-sm">{{ $admin->email ?? '' }}</div>
                </div>
                <div class="flex flex-col items-end">
                    <span class="font-extrabold text-2xl text-lime-700">{{ $admin->services_booked }}</span>
                    <span class="text-xs text-green-800">Services Booked</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
</div>
@endsection