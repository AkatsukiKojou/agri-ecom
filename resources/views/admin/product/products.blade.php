{{-- filepath: resources/views/admin/product/products.blade.php --}}
@extends('admin.layout')

@section('title', 'Product Page')

@section('content')
<div >
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div class="flex items-center gap-3">
            <i class="bi bi-box-seam text-green-600 text-3xl"></i>
            <h2 class="text-2xl font-extrabold tracking-tight text-green-800">Product Listing</h2>
        </div>
        <div class="flex items-center gap-2 mt-2 md:mt-0">
            <input
                type="text"
                id="search"
                placeholder="Search by name..."
                class="border border-green-300 focus:ring-2 focus:ring-green-400 focus:border-green-500 px-4 py-2 rounded-lg shadow-sm transition w-56 text-base h-11"
                onkeyup="filterProducts()"
                style="height:44px;"
            >
            <button
                onclick="document.getElementById('productModal').classList.remove('hidden')"
                class="flex items-center gap-2 bg-green-500 text-white px-5 py-2 rounded-lg shadow hover:bg-green-600 transition font-semibold text-base h-11 min-w-[140px] justify-center"
                style="height:44px;"
            >
                <i class="bi bi-plus-circle text-lg"></i>
                Add Product
            </button>
            <button
                onclick="location.href='{{ route('products.archived.index') }}'"
                class="flex items-center gap-2 bg-yellow-500 text-white px-5 py-2 rounded-lg shadow hover:bg-yellow-600 transition font-semibold text-base h-11"
                style="height:44px;"
            >
                <i class="bi bi-archive text-lg"></i>
                Archive
            </button>
        </div>
    </div>

    {{-- Success Message --}}
    @if(session()->has('message'))
        <div id="successMessage" class="flex items-center gap-2 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded shadow-sm animate-fade-in text-sm">
            <i class="bi bi-check-circle-fill text-xl"></i>
            <span class="font-medium">{{ session('message') }}</span>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const successMessage = document.getElementById('successMessage');
                if (successMessage) {
                    setTimeout(function () {
                        successMessage.style.display = 'none';
                    }, 4000);
                }
            });
        </script>
    @endif

    <style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px);}
        to { opacity: 1; transform: translateY(0);}
    }
    .animate-fade-in { animation: fade-in 0.4s; }
    </style>
    {{-- filepath: resources/views/admin/product/products.blade.php --}}
<style>
    /* Slightly increase font size and column width for better readability */
    #productsTable th, #productsTable td {
        font-size: 15px;
        padding-top: 9px;
        padding-bottom: 9px;
        padding-left: 10px;
        padding-right:10px;
    }
    #productsTable td img {
        width:40px;
        height:40px;
    }
    #productsTable td, #productsTable th {
        max-width: 130px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    #productsTable th {
        font-size: 15px;
    }
</style>


{{-- Filtering --}}
<form id="filterForm" class="flex flex-wrap gap-2 mb-4">
    <select name="type" id="filterType" class="border border-green-300 rounded px-2 py-1 text-sm">
        <option value="">All Types</option>
        <option value="Fertilizer">Fertilizer</option>
        <option value="Vegetables">Vegetables</option>
        <option value="Fruits">Fruits</option>
        <option value="Seeds">Seeds</option>
        <option value="Tools">Tools</option>
        <option value="Pesticides">Pesticides</option>
        <option value="Others">Others</option>
    </select>
    <select name="unit" id="filterUnit" class="border border-green-300 rounded px-2 py-1 text-sm">
        <option value="">All Units</option>
        <option value="">All Units</option>
        <option value="bag">Bag</option>
        <option value="barrel">Barrel</option>
        <option value="box">Box</option>
        <option value="bundle">Bundle</option>
        <option value="cm">Centimeter (cm)</option>
        <option value="dozen">Dozen</option>
        <option value="gallon">Gallon</option>
        <option value="gram">Gram (g)</option>
        <option value="kilogram">Kilogram (kg)</option>
        <option value="liter">Liter (L)</option>
        <option value="meter">Meter (m)</option>
        <option value="ml">Milliliter (ml)</option>
        <option value="ounce">Ounce (oz)</option>
        <option value="pack">Pack</option>
        <option value="pcs">Piece (pcs)</option>
        <option value="pound">Pound (lb)</option>
        <option value="roll">Roll</option>
        <option value="sack">Sack</option>
        <option value="set">Set</option>
        <option value="tray">Tray</option>
        <option value="unit">Unit</option>
        <option value="others">Others</option>
    </select>
    <select name="price" id="filterPrice" class="border border-green-300 rounded px-2 py-1 text-sm">
        <option value="">All Prices</option>
        <option value="0-100">₱0 - ₱100</option>
        <option value="101-500">₱101 - ₱500</option>
        <option value="501-1000">₱501 - ₱1000</option>
        <option value="1001-999999">₱1001+</option>
    </select>
    <select name="stock" id="filterStock" class="border border-green-300 rounded px-2 py-1 text-sm">
        <option value="">Stock: All</option>
        <option value="low-high">Stock: Low to High</option>
        <option value="high-low">Stock: High to Low</option>
    </select>
</form>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    // Trigger AJAX on filter change or search
    $('#filterForm select, #filterForm input').on('change keyup', function() {
        fetchProducts();
    });

    // AJAX pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        fetchProducts(url);
    });

    function fetchProducts(url = "{{ route('products.index') }}") {
        $.ajax({
            url: url,
            type: 'GET',
            data: $('#filterForm').serialize(),
            success: function(data) {
                // Replace the table and pagination
                $('#productsTable tbody').html($(data).find('#productsTable tbody').html());
                $('.pagination').parent().html($(data).find('.pagination').parent().html());
            }
        });
    }
});
</script>
@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Open Add Product modal if validation failed
        var modal = document.getElementById('productModal');
        if (modal) modal.classList.remove('hidden');

        // If first row had 'others' selected previously, show its custom input
        var firstUnit = document.querySelector('select[name="unit[]"]');
        if (firstUnit) {
            // Use server-side old value if present
            var oldUnit = {!! json_encode(old('unit.0')) !!};
            if (oldUnit) {
                firstUnit.value = oldUnit;
            }
            // Ensure custom input is visible if unit is 'others'
            toggleCustomUnit(firstUnit);
            // If there was an old custom value, restore it
            var firstCustom = document.querySelector('input[name="unit_custom[]"]');
            if (firstCustom) {
                var oldCustom = {!! json_encode(old('unit_custom.0')) !!};
                if (oldCustom) firstCustom.value = oldCustom;
            }
            // Restore old stock value if present
            var firstStock = document.querySelector('input[name="stock[]"]');
            if (firstStock) {
                var oldStock = {!! json_encode(old('stock.0')) !!};
                if (oldStock || oldStock === 0) firstStock.value = oldStock;
            }
        }
    });
</script>
@endif



    <!-- ADD Modal -->
    <div id="productModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-11/12 max-w-5xl p-8 relative animate-fade-in">
            <h3 class="text-2xl font-bold mb-6 text-green-700 flex items-center gap-2">
                <i class="bi bi-plus-circle text-green-500"></i> Add New Products
            </h3>
            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6 border-l-4 border-red-500">
                    <strong>Whoops!</strong> There were some issues with your input.<br><br>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="overflow-y-auto max-h-96 border rounded mb-4">
                    <table class="w-full table-auto border" id="productTable">
                        <thead class="bg-gray-200 sticky top-0">
                            <tr>
                                <th class="px-4 py-2">Image</th>
                                <th class="px-4 py-2">Product Name</th>
                                <th class="px-4 py-2">Product Type</th>
                                <th class="px-4 py-2">Unit</th>
                                <th class="px-4 py-2">Price</th>
                                <th class="px-4 py-2">Stock</th>

                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="img-preview-multi" id="product_img_preview_0"></div>
                                    <input type="file" name="image[]" class="border px-2 py-1 w-full rounded" accept="image/*" onchange="previewProductImage(this, 'product_img_preview_0')">
                                </td>
                                <td><input type="text" name="name[]" value="{{ old('name.0') }}" class="border px-2 py-1 w-full rounded" required></td>
                                                                    <td>
                                                                        <select name="type[]" class="border px-2 py-1 w-full rounded" required onchange="toggleCustomType(this)">
                                        <option value="">Select Type</option>
                                        <option value="Fertilizer">Fertilizer</option>
                                        <option value="Vegetables">Vegetables</option>
                                        <option value="Fruits">Fruits</option>
                                        <option value="Seeds">Seeds</option>
                                        <option value="Tools">Tools</option>
                                        <option value="Pesticides">Pesticides</option>
                                        <option value="Others">Others</option>
                                    </select>
                                                                        <input type="text" name="type_custom[]" class="border px-2 py-1 w-full rounded mt-2 custom-type-input" placeholder="Specify type" style="display:none;" />
                                </td>
                                <td>
                                    <select name="unit[]" class="border px-2 py-1 w-full rounded unit-select" required onchange="toggleCustomUnit(this)">
                                        <option value="bag">Bag</option>
                                        <option value="barrel">Barrel</option>
                                        <option value="box">Bottle</option>
                                        <option value="box">Box</option>
                                        <option value="bundle">Bundle</option>
                                        <option value="cm">Centimeter (cm)</option>
                                        <option value="dozen">Dozen</option>
                                        <option value="gallon">Gallon</option>
                                        <option value="gram">Gram (g)</option>
                                        <option value="kilogram">Kilogram (kg)</option>
                                        <option value="liter">Liter (L)</option>
                                        <option value="meter">Meter (m)</option>
                                        <option value="ml">Milliliter (ml)</option>
                                        <option value="ounce">Ounce (oz)</option>
                                        <option value="pack">Pack</option>
                                        <option value="pcs">Piece (pcs)</option>
                                        <option value="pound">Pound (lb)</option>
                                        <option value="roll">Roll</option>
                                        <option value="sack">Sack</option>
                                        <option value="set">Set</option>
                                        <option value="tray">Tray</option>
                                        <option value="unit">Unit</option>
                                        <option value="others">Others</option>
                                    </select>
                                    <input type="text" name="unit_custom[]" class="border px-2 py-1 w-full rounded mt-2 custom-unit-input" placeholder="Specify unit" style="display:none;" />
                                </td>
                               
                                 <td><input type="number" name="price[]" value="{{ old('price.0') }}" step="0.01" class="border px-2 py-1 w-full rounded" required></td>
                                <td><input type="number" name="stock[]" value="{{ old('stock.0') }}" class="border px-2 py-1 w-full rounded" required></td>
                               
                                <td><textarea name="description[]" class="border px-2 py-1 w-full rounded" rows="4" required>{{ old('description.0') }}</textarea></td>
                                <td><button type="button" class="text-red-500 hover:text-red-700 font-semibold" onclick="removeRow(this)">Remove</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button id="addRowBtn" type="button" onclick="addRow()" class="bg-blue-500 text-white px-3 py-1 rounded mb-4 hover:bg-blue-600 transition font-semibold shadow">
                    <i class="bi bi-plus-circle"></i> Add More
                </button>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('productModal').classList.add('hidden')" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition font-semibold">Cancel</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition font-semibold">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT Modal -->
    <div id="editProductModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-11/12 max-w-5xl p-8 relative animate-fade-in">
            <h3 class="text-2xl font-bold mb-6 text-blue-700 flex items-center gap-2">
                <i class="bi bi-pencil-square text-blue-500"></i> Edit Product
            </h3>
<form method="POST" id="editProductForm" enctype="multipart/form-data" action="">
                    @csrf
                    @method('PUT')
                <table class="w-full table-auto border mb-4" id="editProductTable">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="px-4 py-2">Image</th>
                            <th class="px-4 py-2">Product Name</th>
                            <th class="px-4 py-2">Product Type</th>
                            <th class="px-4 py-2">Unit</th>
                            <th class="px-4 py-2">Price</th>

                            <th class="px-4 py-2">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="img-preview-multi" id="edit_product_img_preview"></div>
                                <input type="file" name="image" id="edit_image" class="border px-2 py-1 w-full rounded" accept="image/*" onchange="previewProductImage(this, 'edit_product_img_preview')">
                            </td>
                            <td><input type="text" name="name" id="edit_name" class="border px-2 py-1 w-full rounded" required></td>
                             <td>
                                <select name="type" id="edit_type" class="border px-2 py-1 w-full rounded" required onchange="toggleCustomType(this)">
                                    <option value="">Select Type</option>
                                    <option value="Fertilizer">Fertilizer</option>
                                    <option value="Vegetables">Vegetables</option>
                                    <option value="Fruits">Fruits</option>
                                    <option value="Seeds">Seeds</option>
                                    <option value="Tools">Tools</option>
                                    <option value="Pesticides">Pesticides</option>
                                    <option value="Others">Others</option>
                                </select>
                                <input type="text" id="edit_custom_type" name="custom_type" class="border px-2 py-1 w-full rounded mt-2 custom-type-input" placeholder="Specify type" style="display:none;" />
                                @if($errors->has('custom_type'))
                                    <p class="text-red-600 text-sm mt-1">{{ $errors->first('custom_type') }}</p>
                                @endif
                            </td>
                            <td>
                                <select name="unit" id="edit_unit" class="border px-2 py-1 w-full rounded unit-select" required onchange="toggleCustomUnit(this)">
                                    <option value="bag">Bag</option>
                                    <option value="barrel">Barrel</option>
                                    <option value="box">Box</option>
                                    <option value="bundle">Bundle</option>
                                    <option value="cm">Centimeter (cm)</option>
                                    <option value="dozen">Dozen</option>
                                    <option value="gallon">Gallon</option>
                                    <option value="gram">Gram (g)</option>
                                    <option value="kilogram">Kilogram (kg)</option>
                                    <option value="liter">Liter (L)</option>
                                    <option value="meter">Meter (m)</option>
                                    <option value="ml">Milliliter (ml)</option>
                                    <option value="ounce">Ounce (oz)</option>
                                    <option value="pack">Pack</option>
                                    <option value="pcs">Piece (pcs)</option>
                                    <option value="pound">Pound (lb)</option>
                                    <option value="roll">Roll</option>
                                    <option value="sack">Sack</option>
                                    <option value="set">Set</option>
                                    <option value="tray">Tray</option>
                                    <option value="unit">Unit</option>
                                    <option value="others">Others</option>
                                </select>
                                <input type="text" id="edit_custom_unit" name="custom_unit" class="border px-2 py-1 w-full rounded mt-2 custom-unit-input" placeholder="Specify unit" style="display:none;" />
                            </td>
                           
                            <td><input type="number" name="price" id="edit_price" step="0.01" class="border px-2 py-1 w-full rounded" required></td>
                            
                                <td><textarea name="description" id="edit_description" class="border px-2 py-1 w-full rounded" rows="4" required></textarea></td>
                        </tr>
                    </tbody>
                </table>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition font-semibold">Cancel</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition font-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div id="addStockModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative animate-fade-in">
            <h3 class="text-2xl font-bold mb-6 text-green-700 flex items-center gap-2">
                <i class="bi bi-plus-circle text-green-500"></i> Add Stock
            </h3>
            <form id="addStockForm" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="addStockProductId">
                <div class="mb-4">
                    <label class="block mb-1 font-semibold text-gray-700">Current Stock:</label>
                    <input type="number" id="currentStock" class="border px-2 py-1 w-full bg-gray-100 rounded" readonly>
                </div>
                <div class="mb-4">
                    <label class="block mb-1 font-semibold text-gray-700">Add Quantity:</label>
                    <input type="number" name="add_quantity" id="addQuantity" class="border px-2 py-1 w-full rounded" min="1" required>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeAddStockModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition font-semibold">Cancel</button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition font-semibold">Add</button>
                </div>
            </form>
        </div>
    </div>
    <!-- Reduce Stock Modal -->
<div id="reduceStockModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 relative animate-fade-in">
        <h3 class="text-2xl font-bold mb-6 text-red-700 flex items-center gap-2">
            <i class="bi bi-dash-circle text-red-500"></i> Reduce Stock
        </h3>
        <form id="reduceStockForm" method="POST" action="">
            @csrf
            <input type="hidden" name="product_id" id="reduceStockProductId">
            <div class="mb-4">
                <label class="block mb-1 font-semibold text-gray-700">Current Stock:</label>
                <input type="number" id="reduceCurrentStock" class="border px-2 py-1 w-full bg-gray-100 rounded" readonly>
            </div>
            <div class="mb-4">
                <label class="block mb-1 font-semibold text-gray-700">Reduce Quantity:</label>
                <input type="number" name="reduce_quantity" id="reduceQuantity" class="border px-2 py-1 w-full rounded" min="1" required max="">
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeReduceStockModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition font-semibold">Cancel</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition font-semibold">Reduce</button>
            </div>
        </form>
    </div>
</div>
    {{-- Archive selection --}}
<form id="bulkArchiveForm" action="{{ route('products.bulkArchive') }}" method="POST" class="mb-2">
    @csrf
    <button type="submit"
        id="archiveSelectedBtn"
        style="display:none"
        class="bg-yellow-600 text-white px-3 py-1 rounded shadow hover:bg-yellow-700 transition font-semibold text-sm"
        onclick="return confirm('Archive selected products?')">
        <i class="bi bi-archive"></i> Archive Selected
    </button>
    <!-- Your table here -->

   <!-- Products Table -->
    <div class="overflow-x-auto rounded-xl shadow mt-4">
        <table id="productsTable" class="w-full table-auto border rounded-xl overflow-hidden text-xs">
            <thead>
                <tr class="bg-gradient-to-r from-green-600 to-green-400 text-white uppercase tracking-wide">
                     <th class="px-2 py-2 border text-center">
                        <input type="checkbox" id="selectAllArchive">
                    </th>
                    <th class="px-2 py-2 border">Img</th>
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
                @foreach($products as $product)
                    <tr class="even:bg-gray-50 hover:bg-green-50 transition">
                        <td class="px-2 py-1 border text-center">
                            <input type="checkbox" class="archive-checkbox" name="archive_ids[]" value="{{ $product->id }}">
                        </td>
                        <td class="px-2 py-1 border text-center">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-8 h-8 object-cover rounded shadow mx-auto">
                            @else
                                <span class="text-gray-400 italic">No Img</span>
                            @endif
                        </td>
                        <td class="px-2 py-1 border font-semibold text-gray-800 truncate max-w-[90px]">{{ $product->name }}</td>
                        <td class="px-2 py-1 border text-gray-600 truncate max-w-[70px]">
                            {{ $product->type ?? 'N/A' }}
                        </td>
                        <td class="px-2 py-1 border text-gray-600">{{ ucfirst($product->unit) }}</td>
                        <td class="px-2 py-1 border text-green-700 font-bold">₱{{ number_format($product->price, 2) }}</td>
                        <td class="px-2 py-1 border">
                            <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold text-xs shadow">{{ $product->stock_quantity }}</span>
                        </td>
                        <td class="px-2 py-1 border text-gray-600 truncate max-w-[80px]" title="{{ $product->description }}">
                            {{ Str::limit($product->description, 10 ) }}
                        </td>
                        <td class="px-2 py-1 border text-center">
                            <div class="flex flex-row items-center justify-center gap-1">
                                <button type="button"
                                    class="bg-green-500 text-white p-1 rounded hover:bg-green-600 transition shadow text-xs"
                                    title="Add Stock"
                                    onclick="openAddStockModal({{ $product->id }}, {{ $product->stock_quantity }})">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                                <button type="button"
                                    class="bg-red-500 text-white p-1 rounded hover:bg-red-600 transition shadow text-xs"
                                    title="Reduce Stock"
                                    onclick="openReduceStockModal({{ $product->id }}, {{ $product->stock_quantity }})">
                                    <i class="bi bi-dash-circle"></i>
                                </button>
                                <button type="button"
                                    class="bg-blue-500 text-white p-1 rounded hover:bg-blue-600 transition shadow text-xs"
                                    title="Edit"
                                    onclick="openEditModal({{ $product->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                               
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    </form>
</div>
@if ($products->hasPages())
    <nav class="mt-8 flex justify-center">
        <ul class="inline-flex items-center -space-x-px">
            {{-- Previous Page Link --}}
            @if ($products->onFirstPage())
                <li>
                    <span class="px-3 py-2 ml-0 leading-tight text-gray-400 bg-white border border-gray-300 rounded-l-lg cursor-not-allowed">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $products->previousPageUrl() }}" rel="prev"
                       class="px-3 py-2 ml-0 leading-tight text-green-700 bg-white border border-gray-300 rounded-l-lg hover:bg-green-100 hover:text-green-900 transition">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($products->links()->elements[0] as $page => $url)
                @if ($page == $products->currentPage())
                    <li>
                        <span class="px-3 py-2 leading-tight text-white bg-green-600 border border-green-600">{{ $page }}</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $url }}" class="px-3 py-2 leading-tight text-green-700 bg-white border border-gray-300 hover:bg-green-100 hover:text-green-900 transition">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($products->hasMorePages())
                <li>
                    <a href="{{ $products->nextPageUrl() }}" rel="next"
                       class="px-3 py-2 leading-tight text-green-700 bg-white border border-gray-300 rounded-r-lg hover:bg-green-100 hover:text-green-900 transition">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li>
                    <span class="px-3 py-2 leading-tight text-gray-400 bg-white border border-gray-300 rounded-r-lg cursor-not-allowed">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
<script>
    function previewProductImage(input, previewId) {
        const preview = document.getElementById(previewId);
        preview.innerHTML = '';
        if (input.files && input.files[0]) {
            const file = input.files[0];
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '60px';
                img.style.height = '60px';
                img.style.objectFit = 'cover';
                img.style.borderRadius = '0.5rem';
                img.style.border = '1px solid #e5e7eb';
                img.style.boxShadow = '0 1px 4px #0001';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    }

    // Toggle custom unit input when 'others' selected
    function toggleCustomUnit(selectEl) {
        if (!selectEl) return;
        var cell = selectEl.closest('td');
        if (!cell) return;
        var custom = cell.querySelector('.custom-unit-input');
        if (!custom) return;
        if (selectEl.value !== 'others') {
            custom.style.display = 'none';
            custom.required = false;
            custom.value = '';
            return;
        }
        custom.style.display = '';
        custom.required = true;
    }

    // Toggle custom type input when 'Others' selected
    function toggleCustomType(selectEl) {
        if (!selectEl) return;
        var cell = selectEl.closest('td');
        if (!cell) return;
        var custom = cell.querySelector('.custom-type-input');
        if (!custom) return;
        if ((selectEl.value || '').toLowerCase() !== 'others') {
            custom.style.display = 'none';
            custom.required = false;
            custom.value = '';
            return;
        }
        custom.style.display = '';
        custom.required = true;
    }


    function addRow() {
        const tableBody = document.querySelector("#productTable tbody");
        const rowIndex = tableBody.querySelectorAll('tr').length;
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <div class="img-preview-multi" id="product_img_preview_${rowIndex}"></div>
                <input type="file" name="image[]" class="border px-2 py-1 w-full rounded" accept="image/*" onchange="previewProductImage(this, 'product_img_preview_${rowIndex}')">
            </td>
            <td><input type="text" name="name[]" class="border px-2 py-1 w-full rounded" required></td>
                                                <td>
                                                <select name="type[]" class="border px-2 py-1 w-full rounded" required onchange="toggleCustomType(this)">
                <option value="">Select Type</option>
                <option value="Fertilizer">Fertilizer</option>
                <option value="Vegetables">Vegetables</option>
                <option value="Fruits">Fruits</option>
                <option value="Seeds">Seeds</option>
                <option value="Tools">Tools</option>
                 <option value="Pesticides">Pesticides</option>
                 <option value="Others">Others</option>
                </select>
                                                <input type="text" name="type_custom[]" class="border px-2 py-1 w-full rounded mt-2 custom-type-input" placeholder="Specify type" style="display:none;" />
                                            </td>
            <td>
                <select name="unit[]" class="border px-2 py-1 w-full rounded unit-select" required onchange="toggleCustomUnit(this)">
                    <option value="bag">Bag</option>
                    <option value="barrel">Barrel</option>
                    <option value="box">Box</option>
                    <option value="bundle">Bundle</option>
                    <option value="cm">Centimeter (cm)</option>
                    <option value="dozen">Dozen</option>
                    <option value="gallon">Gallon</option>
                    <option value="gram">Gram (g)</option>
                    <option value="kilogram">Kilogram (kg)</option>
                    <option value="liter">Liter (L)</option>
                    <option value="meter">Meter (m)</option>
                    <option value="ml">Milliliter (ml)</option>
                    <option value="ounce">Ounce (oz)</option>
                    <option value="pack">Pack</option>
                    <option value="pcs">Piece (pcs)</option>
                    <option value="pound">Pound (lb)</option>
                    <option value="roll">Roll</option>
                    <option value="sack">Sack</option>
                    <option value="set">Set</option>
                    <option value="tray">Tray</option>
                    <option value="unit">Unit</option>
                    <option value="others">Others</option>
                </select>
                <input type="text" name="unit_custom[]" class="border px-2 py-1 w-full rounded mt-2 custom-unit-input" placeholder="Specify unit" style="display:none;" />
            </td>
            <td><input type="number" name="price[]" step="0.01" class="border px-2 py-1 w-full rounded" required></td>
            <td><input type="number" name="stock[]" class="border px-2 py-1 w-full rounded" required></td>

            <td><textarea name="description[]" class="border px-2 py-1 w-full rounded" rows="4" required></textarea></td>
            <td><button type="button" class="text-red-500 hover:text-red-700 font-semibold" onclick="removeRow(this)">Remove</button></td>
        `;
        tableBody.appendChild(newRow);
    }

    function removeRow(button) {
        const row = button.closest('tr');
        row.remove();
    }

    function filterProducts() {
        const searchValue = document.getElementById('search').value.toLowerCase();
        const rows = document.querySelectorAll('#productsTable tbody tr');
        rows.forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            const matchesSearch = name.includes(searchValue);
            row.style.display = matchesSearch ? '' : 'none';
        });
    }

    function openEditModal(id) {
        fetch(`/products/${id}/edit`)
            .then(response => response.json())
            .then(data => {
document.getElementById('editProductForm').action = `/admin/products/${id}`; 

             document.getElementById('edit_name').value = data.name;
                // Handle type & custom type
                var editType = document.getElementById('edit_type');
                var editCustomType = document.getElementById('edit_custom_type');
                var typeFound = Array.from(editType.options).some(o => o.value === data.type);
                if (typeFound) {
                    editType.value = data.type;
                    if (editCustomType) { editCustomType.style.display = 'none'; editCustomType.value = ''; editCustomType.required = false; }
                } else {
                    editType.value = 'Others';
                    if (editCustomType) { editCustomType.style.display = ''; editCustomType.value = data.type; editCustomType.required = true; }
                }
                // If data.unit matches one of the options, select it; otherwise select 'others' and show custom input
                var editUnit = document.getElementById('edit_unit');
                var editCustom = document.getElementById('edit_custom_unit');
                var found = Array.from(editUnit.options).some(o => o.value === data.unit);
                if (found) {
                    editUnit.value = data.unit;
                    if (editCustom) { editCustom.style.display = 'none'; editCustom.value = ''; editCustom.required = false; }
                } else {
                    editUnit.value = 'others';
                    if (editCustom) { editCustom.style.display = ''; editCustom.value = data.unit; editCustom.required = true; }
                }
                document.getElementById('edit_price').value = data.price;
                document.getElementById('edit_description').value = data.description;
                // Show current image preview
                const preview = document.getElementById('edit_product_img_preview');
                preview.innerHTML = '';
                if (data.image) {
                    const img = document.createElement('img');
                    img.src = `/storage/${data.image}`;
                    img.style.width = '60px';
                    img.style.height = '60px';
                    img.style.objectFit = 'cover';
                    img.style.borderRadius = '0.5rem';
                    img.style.border = '1px solid #e5e7eb';
                    img.style.boxShadow = '0 1px 4px #0001';
                    preview.appendChild(img);
                }
                document.getElementById('editProductModal').classList.remove('hidden');
            })
            .catch(error => {
                alert('Failed to fetch product data.');
                console.error(error);
            });
    }

    function closeEditModal() {
        document.getElementById('editProductModal').classList.add('hidden');
    }

    function openAddStockModal(productId, currentStock) {
        document.getElementById('addStockProductId').value = productId;
        document.getElementById('currentStock').value = currentStock;
        document.getElementById('addQuantity').value = '';
        document.getElementById('addStockModal').classList.remove('hidden');
        document.getElementById('addStockForm').action = `/products/${productId}/add-stock`;
    }
    function closeAddStockModal() {
        document.getElementById('addStockModal').classList.add('hidden');
    }
    function openReduceStockModal(productId, currentStock) {
        document.getElementById('reduceStockProductId').value = productId;
        document.getElementById('reduceCurrentStock').value = currentStock;
        document.getElementById('reduceQuantity').value = '';
        document.getElementById('reduceQuantity').max = currentStock;
        document.getElementById('reduceStockModal').classList.remove('hidden');
        document.getElementById('reduceStockForm').action = `/admin/products/${productId}/reduce-stock`;
    }
function closeReduceStockModal() {
    document.getElementById('reduceStockModal').classList.add('hidden');
}
</script>

{{-- archive  --}}
<script>
$(document).ready(function() {
    // Select All Checkbox
    $('#selectAllArchive').on('change', function() {
        $('.archive-checkbox').prop('checked', this.checked).trigger('change');
    });

    // Show/hide Archive Selected button
    $('.archive-checkbox, #selectAllArchive').on('change', function() {
        if ($('.archive-checkbox:checked').length > 0) {
            $('#archiveSelectedBtn').show();
        } else {
            $('#archiveSelectedBtn').hide();
        }
    });

    // Prevent submit if nothing is checked
    $('#bulkArchiveForm').on('submit', function(e) {
        if ($('.archive-checkbox:checked').length === 0) {
            alert('Please select at least one product to archive.');
            e.preventDefault();
        }
    });
});
</script>
@endsection