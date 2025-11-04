<?php
// app/Http/Controllers/MessageController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Show chat with a specific user (for both admin and user)

public function fetch($adminId) {
    $userId = Auth::id();
    $messages = Message::where(function($q) use ($userId, $adminId) {
        $q->where('sender_id', $userId)->where('receiver_id', $adminId);
    })->orWhere(function($q) use ($userId, $adminId) {
        $q->where('sender_id', $adminId)->where('receiver_id', $userId);
    })
    ->orderBy('created_at')
    ->get();

    return response()->json($messages);
}

// public function send(Request $request, $adminId) {
//     $request->validate(['message' => 'required|string']);
//     $userId = Auth::id();

//     $message = Message::create([
//         'sender_id' => $userId,
//         'receiver_id' => $adminId,
//         'message' => $request->message,
//     ]);

//     return response()->json($message);
// }

public function send(Request $request, $adminId)
{
    $request->validate([
        'message' => 'nullable|string',
        'image' => 'nullable|image|max:2048'
    ]);
    $userId = Auth::id();

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('chat_images', 'public');
    }
    $message = Message::create([
        'sender_id' => $userId,
        'receiver_id' => $adminId,
        'message' => $request->message,
        'image' => $imagePath,
        // 'service_id' => $request->service_id,
        // 'service_name' => $request->service_name,
        // 'service_image' => $request->service_image,
        // 'service_type' => $request->service_type,
        // 'service_price' => $request->service_price,
    ]);

    return response()->json($message);
}

    // For admin
public function adminIndex()
{
    $adminId = Auth::id();
    $userMessages = Message::where('receiver_id', $adminId)
        ->with('sender')
        ->latest()
        ->get()
        ->groupBy('sender_id');

    return view('admin.messages.index', compact('userMessages'));
}

public function adminChat(User $user)
{
    $adminId = Auth::id();
    $messages = Message::where(function($q) use ($user, $adminId) {
            $q->where('sender_id', $user->id)->where('receiver_id', $adminId);
        })->orWhere(function($q) use ($user, $adminId) {
            $q->where('sender_id', $adminId)->where('receiver_id', $user->id);
        })
        ->orderBy('created_at')
        ->get();

    return view('admin.messages.chat', compact('messages', 'user'));
}

public function adminFetch($userId)
{
    $adminId = Auth::id();

    // Mark all messages as read where admin is the receiver and sender is the user
    Message::where('sender_id', $userId)
        ->where('receiver_id', $adminId)
        ->where('is_read', 0)
        ->update(['is_read' => 1]);

    $messages = Message::where(function($q) use ($adminId, $userId) {
        $q->where('sender_id', $adminId)->where('receiver_id', $userId);
    })->orWhere(function($q) use ($adminId, $userId) {
        $q->where('sender_id', $userId)->where('receiver_id', $adminId);
    })
    ->orderBy('created_at')
    ->with('sender')
    ->get();

    return response()->json($messages);
}

public function adminSend(Request $request, $userId)
{
    $request->validate([
        'message' => 'nullable|string',
        'image' => 'nullable|image|max:2048'
    ]);    $adminId = Auth::id();

    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('chat_images', 'public');
    }
    $message = Message::create([
        'sender_id' => $adminId,
        'receiver_id' => $userId,
        'message' => $request->message,
        'image' => $imagePath,

    ]);

    return response()->json($message);
}
public function adminDeleteConversation($userId)
{
    $adminId = Auth::id();
    // Mark as deleted for admin only (soft delete or flag)
    Message::where(function($q) use ($adminId, $userId) {
        $q->where('sender_id', $adminId)->where('receiver_id', $userId);
    })->orWhere(function($q) use ($adminId, $userId) {
        $q->where('sender_id', $userId)->where('receiver_id', $adminId);
    })->update(['deleted_by_admin' => true]); // Add this column to your messages table

    return response()->json(['success' => true]);
}
// public function unreadCount()
// {
//     $count = \App\Models\Message::where('receiver_id', Auth::id())
//         ->where('is_read', 0)
//         ->count();

//     return response()->json(['count' => $count]);
// }
}