@extends('user.layout')

@section('content')
<div class="max-w-xl mx-auto p-4 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Send a Message to {{ $admin->name }}</h2>

    <form action="{{ route('user.message.store') }}" method="POST">
        @csrf
        <input type="hidden" name="admin_id" value="{{ $admin->id }}">

        <textarea name="message" rows="5" class="w-full border p-2 rounded" placeholder="Your message..."></textarea>

        <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Send Message
        </button>
    </form>
</div>
@endsection
