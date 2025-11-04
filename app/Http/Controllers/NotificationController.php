<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        // Mark all unread notifications as read for the logged-in user
        $request->user()->unreadNotifications->markAsRead();

// Sa controller o view composer
$notifications = Auth::user()->unreadNotifications;
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
