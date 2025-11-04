@extends('superadmin.layout')
@section('title', 'Add Admin')
@section('content')
<div class="max-w-md mx-auto py-10">
    <h1 class="text-xl font-bold mb-6">Add Admin</h1>
    <form method="POST" action="{{ route('manageadmins.store') }}">
        @csrf
        @if(session('warning'))
            <div class="mb-4 text-yellow-700 bg-yellow-100 px-3 py-2 rounded">{{ session('warning') }}</div>
        @endif
        @if(session('success'))
            <div id="successToastInline" class="mb-4 text-green-700 bg-green-100 px-3 py-2 rounded">{{ session('success') }}</div>
        @endif
        <div class="mb-4">
            <label class="block mb-1">Name</label>
            <input type="text" name="name" class="w-full border px-3 py-2 rounded" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Email</label>
            <input type="email" name="email" class="w-full border px-3 py-2 rounded" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Password</label>
            <input type="password" name="password" class="w-full border px-3 py-2 rounded" required>
        </div>
        <div class="mb-4">
            <label class="block mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" class="w-full border px-3 py-2 rounded" required>
        </div>
        <button id="submitBtn" type="submit" class="bg-green-700 text-white px-4 py-2 rounded">Add Admin</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const btn = document.getElementById('submitBtn');
    if (form && btn) {
        form.addEventListener('submit', function () {
            btn.disabled = true;
            btn.innerText = 'Adding...';
        });
    }
});
</script>
<!-- Toast container -->
@if(session('success'))
    <div id="successToast" class="fixed top-6 right-6 z-50 transform transition-all duration-300 opacity-0 translate-y-2">
        <div class="bg-green-600 text-white px-4 py-3 rounded shadow-lg flex items-start gap-3">
            <div class="flex-1">
                <div class="font-semibold">Success</div>
                <div class="text-sm">{{ session('success') }}</div>
            </div>
            <button id="closeToast" class="text-white opacity-90 hover:opacity-100">&times;</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const toast = document.getElementById('successToast');
            const close = document.getElementById('closeToast');
            if (toast) {
                // show
                setTimeout(() => {
                    toast.classList.remove('opacity-0');
                    toast.classList.remove('translate-y-2');
                    toast.classList.add('opacity-100');
                    toast.classList.add('translate-y-0');
                }, 50);
                // auto-hide after 3.5s
                const hideFn = () => {
                    toast.classList.remove('opacity-100');
                    toast.classList.add('opacity-0');
                    toast.classList.add('translate-y-2');
                    setTimeout(() => { try{ toast.remove(); } catch(e){} }, 300);
                };
                const timer = setTimeout(hideFn, 3500);
                if (close) close.addEventListener('click', function(){ clearTimeout(timer); hideFn(); });
            }
        });
    </script>
@endif
@endsection