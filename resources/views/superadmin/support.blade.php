{{-- filepath: resources/views/superadmin/support.blade.php --}}
@extends('superadmin.layout')

@section('title', 'Support & Communication')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg p-8 mt-8">
    <h1 class="text-2xl font-bold mb-4 text-green-900">Support & Communication</h1>
    <p class="mb-6 text-green-700">Contact support or view communication requests below.</p>
    <form method="POST" action="#">
        <div class="mb-4">
            <label class="block text-green-900 font-semibold mb-2">Your Name</label>
            <input type="text" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Enter your name...">
        </div>
        <div class="mb-4">
            <label class="block text-green-900 font-semibold mb-2">Email</label>
            <input type="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" placeholder="Enter your email...">
        </div>
        <div class="mb-4">
            <label class="block text-green-900 font-semibold mb-2">Message</label>
            <textarea class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400" rows="4" placeholder="Type your message..."></textarea>
        </div>
        <button type="submit" class="bg-green-700 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-800 transition">Send Message</button>
    </form>
    <!-- You can add a table/list of support tickets below -->
</div>
@endsection
