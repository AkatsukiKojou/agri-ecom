{{-- resources/views/messages/inbox.blade.php --}}
@extends('user.layout')
@section('content')
<h2>Inbox</h2>
@foreach($messages as $msg)
    <div>
        <strong>{{ $msg->sender->name }}:</strong>
        <p>{{ $msg->message }}</p>
        <small>{{ $msg->created_at }}</small>
    </div>
@endforeach

<form action="{{ route('messages.send', ['adminId' => $admin_id]) }}" method="POST">
    @csrf
    <input type="hidden" name="receiver_id" value="{{ $admin_id }}"> {{-- admin id here --}}
    <textarea name="message" required></textarea>
    <button type="submit">Send</button>
</form>
@endsection