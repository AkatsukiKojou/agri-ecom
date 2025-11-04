@extends('admin.layout')

@section('content')
  <div class="max-w-6xl mx-auto">
    
  <!-- Header -->
  <h1 class="text-3xl font-bold text-green-800 mb-2 text-center">Inventory Management</h1>
  <p class="text-gray-600 mb-6 text-center">Monitor and update your stock records easily.</p>

    <!-- Flash message (server-side or JS) -->
    @if(session('success') || session('error'))
      <div id="flashMessage" aria-live="polite" class="mb-4 p-3 rounded-lg {{ session('success') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ session('success') ?? session('error') }}</div>
    @else
      <div id="flashMessage" aria-live="polite" class="sr-only"></div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div class="bg-green-600 text-white p-5 rounded-2xl shadow">
        <p class="text-sm">Total Products</p>
        <h2 class="text-3xl font-bold mt-1">{{ $totalProducts ?? 0 }}</h2>
      </div>
      <div class="bg-green-600 text-white p-5 rounded-2xl shadow">
        <p class="text-sm">Total Stock</p>
        <h2 class="text-3xl font-bold mt-1">{{ number_format($totalStock ?? 0) }}</h2>
      </div>
      <div class="bg-green-600 text-white p-5 rounded-2xl shadow">
        <p class="text-sm">Low Stock</p>
        <h2 class="text-3xl font-bold mt-1">{{ $lowStockProducts->count() ?? 0 }}</h2>
      </div>
      <div class="bg-green-600 text-white p-5 rounded-2xl shadow">
        <p class="text-sm">Stock-In (This Month)</p>
        <h2 class="text-3xl font-bold mt-1">{{ number_format($stockInThisMonth ?? 0) }}</h2>
      </div>
    </div>

    <!-- Add Stock Section -->
    <div class="bg-white rounded-2xl shadow p-6 mb-8">
      <h2 class="text-xl font-semibold text-green-700 mb-4">➕ Add New Stock</h2>
      <form id="stockInForm" action="{{ route('admin.inventory.storeStock') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4" novalidate>
        @csrf
        
        <!-- Product -->
        <div>
          <label for="product_id" class="block text-sm text-gray-700 mb-1">Product</label>
          <select id="product_id" name="product_id" aria-describedby="error_product" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400" required>
            <option value="">Select Product</option>
            @if(isset($products) && $products->count())
              @foreach($products as $p)
                <option value="{{ $p->id }}">{{ $p->name }}{{ $p->unit ? ' ('.$p->unit.')' : '' }}</option>
              @endforeach
            @else
              <option value="">No products available</option>
            @endif
          </select>
          <p id="error_product" class="text-red-600 text-sm mt-1 hidden" role="alert">Please select a product.</p>
        </div>

        <!-- Quantity -->
        <div>
          <label for="quantity_added" class="block text-sm text-gray-700 mb-1">Quantity Added</label>
          <input id="quantity_added" type="number" name="quantity_added" placeholder="e.g. 50" aria-describedby="error_quantity" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400" required>
          <p id="error_quantity" class="text-red-600 text-sm mt-1 hidden" role="alert">Please enter a quantity (minimum 1).</p>
        </div>

        <!-- Date Received -->
        <div>
          <label for="date_received" class="block text-sm text-gray-700 mb-1">Date Received</label>
          <input id="date_received" type="date" name="date_received" aria-describedby="error_date" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400" value="{{ old('date_received', now()->format('Y-m-d')) }}" required>
          <p id="error_date" class="text-red-600 text-sm mt-1 hidden" role="alert">Please select a valid date.</p>
        </div>

        <!-- Remarks -->
        <div>
          <label for="remarks" class="block text-sm text-gray-700 mb-1">Remarks (optional)</label>
          <input id="remarks" type="text" name="remarks" placeholder="e.g. From supplier" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400">
        </div>

        <!-- Submit Button -->
        <div class="md:col-span-2 text-right">
          <button id="submitBtn" type="submit" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition">
            Add Stock
          </button>
        </div>
      </form>
    </div>

    <!-- Low Stock Items (Moved Up) -->
    <div class="bg-white rounded-2xl shadow p-6 mb-8">
      <h2 class="text-xl font-semibold text-green-700 mb-4">⚠️ Low Stock Items</h2>
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-green-100 text-green-700">
            <th class="p-3">Image</th>
            <th class="p-3">Product</th>
            <th class="p-3">Type</th>
            <th class="p-3">Unit</th>
            <th class="p-3">Stock</th>
            <th class="p-3">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lowStockProducts as $p)
            <tr class="border-t">
              <td class="p-3">
                @if(!empty($p->image))
                  <img src="{{ asset('storage/'.$p->image) }}" alt="{{ $p->name }}" class="w-12 h-12 object-cover rounded" />
                @else
                  <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-500">No image</div>
                @endif
              </td>
              <td class="p-3">{{ $p->name }}</td>
              <td class="p-3">{{ $p->type ?? '-' }}</td>
              <td class="p-3">{{ $p->unit ?? '-' }}</td>
              <td class="p-3">{{ $p->stock_quantity ?? 0 }}</td>
              @php
                $threshold = $p->reorder_level ?? 5;
                $isCritical = ($p->stock_quantity ?? 0) <= 1;
                $isLow = ($p->stock_quantity ?? 0) <= $threshold && ($p->stock_quantity ?? 0) > 1;
              @endphp
              <td class="p-3 {{ $isCritical ? 'text-red-600' : ($isLow ? 'text-yellow-600' : 'text-gray-600') }} font-semibold">
                {{ $isCritical ? 'Critical' : ($isLow ? 'Low' : 'OK') }}
              </td>
            </tr>
          @empty
            <tr class="border-t"><td class="p-3" colspan="6">No low-stock items.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Stock-In History (Moved Down) -->
    <div class="bg-white rounded-2xl shadow p-6 mb-8">
      <h2 class="text-xl font-semibold text-green-700 mb-4">📜 Stock-In History</h2>
      <div class="mb-4 flex items-center gap-3">
        <input id="searchHistory" type="search" placeholder="Search history (product, remarks, date)" class="w-full md:w-1/3 border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-green-400" aria-label="Search stock-in history">
      </div>
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-green-100 text-green-700">
            <th class="p-3">Date</th>
            <th class="p-3">Img</th>
            <th class="p-3">Product</th>
            <th class="p-3">Type</th>
            <th class="p-3">Quantity Added</th>
            <th class="p-3">Remarks</th>
          </tr>
        </thead>
        <tbody id="historyTableBody">
          @forelse($stockIns as $s)
            <tr class="border-t">
              <td class="p-3">{{ optional($s->date_received)->format('Y-m-d') ?? $s->created_at->format('Y-m-d') }}</td>
              <td class="p-3"> @if(!empty($s->product) && !empty($s->product->image))
                  <img src="{{ asset('storage/'.$s->product->image) }}" alt="{{ $s->product->name }}" class="w-12 h-12 object-cover rounded" />
                @else
                  <div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-500">No image</div>
                @endif</td>
              <td class="p-3">{{ $s->product->name ?? '—' }}</td>
              <td class="p-3">{{ $s->product->type ?? '-' }}</td>
              <td class="p-3">@if($s->quantity > 0)+@elseif($s->quantity < 0)-@endif{{ number_format(abs($s->quantity)) }} {{ $s->product->unit ?? '' }}</td>
              <td class="p-3 text-gray-600">{{ $s->remarks }}</td>
            </tr>
          @empty
            <tr class="border-t"><td class="p-3" colspan="6">No stock-in history found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>

  <script>
    // UI-only: client-side validation and history search/filter
    document.addEventListener('DOMContentLoaded', function(){
      const form = document.getElementById('stockInForm');
      const submitBtn = document.getElementById('submitBtn');
      const product = document.getElementById('product_id');
      const quantity = document.getElementById('quantity_added');
      const date = document.getElementById('date_received');
      const flash = document.getElementById('flashMessage');

      function showError(id){
        const el = document.getElementById(id);
        if(!el) return;
        el.classList.remove('hidden');
        el.classList.add('block');
      }
      function hideError(id){
        const el = document.getElementById(id);
        if(!el) return;
        el.classList.add('hidden');
        el.classList.remove('block');
      }

      form.addEventListener('submit', function(e){
        let valid = true;
        if(!product.value){ showError('error_product'); valid = false; } else hideError('error_product');
        const q = parseInt(quantity.value,10);
        if(!quantity.value || isNaN(q) || q < 1){ showError('error_quantity'); valid = false; } else hideError('error_quantity');
        if(!date.value){ showError('error_date'); valid = false; } else hideError('error_date');

        if(!valid){
          e.preventDefault();
          if(!document.getElementById('error_product').classList.contains('hidden')){ product.focus(); }
          else if(!document.getElementById('error_quantity').classList.contains('hidden')){ quantity.focus(); }
          else if(!document.getElementById('error_date').classList.contains('hidden')){ date.focus(); }
          flash.classList.remove('sr-only');
          flash.textContent = 'Please fix the highlighted errors.';
          setTimeout(()=>{ flash.classList.add('sr-only'); flash.textContent=''; }, 3000);
        } else {
          // allow the form to submit to the backend; disable the button to prevent double submit
          submitBtn.disabled = true;
          submitBtn.textContent = 'Adding...';
          // no e.preventDefault() here so submission proceeds
        }
      });

      // Search/filter for Stock-In History
      const search = document.getElementById('searchHistory');
      const clearBtn = document.getElementById('clearSearch');
      const tbody = document.getElementById('historyTableBody');
      if(search && tbody){
        search.addEventListener('input', function(){
          const q = this.value.toLowerCase().trim();
          Array.from(tbody.querySelectorAll('tr')).forEach(tr=>{
            const text = tr.textContent.toLowerCase();
            tr.style.display = text.indexOf(q) === -1 ? 'none' : '';
          });
        });
        clearBtn.addEventListener('click', function(){ search.value=''; search.dispatchEvent(new Event('input')); search.focus(); });
      }
    });
  </script>
</body>
</html>
@endsection