<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
 public function boot()
{
    View::composer('user.layout', function ($view) {
        $user = Auth::user();
        $messages = collect();
        $unreadCount = 0;
        if ($user) {
            $messages = Message::where('user_id', $user->id)
                ->with('admin')
                ->latest()
                ->take(5)
                ->get();
            $unreadCount = Message::where('user_id', $user->id)
                ->where('is_unread', true)
                ->count();
        }
        $view->with(compact('messages', 'unreadCount'));
    });
}
  



}
