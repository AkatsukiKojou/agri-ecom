{{-- resources/views/messages/admin_inbox.blade.php --}}
@extends('admin.layout')
@section('content')
<h2>User Messages</h2>
@foreach($messages as $msg)
    <div>
        <strong>{{ $msg->sender->name }}:</strong>
        <p>{{ $msg->message }}</p>
        <small>{{ $msg->created_at }}</small>
    </div>
@endforeach

<form action="{{ route('messages.send') }}" method="POST">
    @csrf
    <select name="receiver_id" required>
        @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endforeach
    </select>
    <textarea name="message" required></textarea>
    <button type="submit">Send</button>
</form>
@endsection