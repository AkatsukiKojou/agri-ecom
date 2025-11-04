<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Profile;

class UserFollowedNotification extends Notification
{
    use Queueable;

    public $follower;
    public $profile;
      protected $user;

    public function __construct(User $follower, Profile $profile)
    {
        $this->follower = $follower;
        $this->profile = $profile;
      
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "{$this->follower->name} followed your shop profile.",
            'follower_id' => $this->follower->id,
            'profile_id' => $this->profile->id,
             'type' => 'follow',
        ];
    }
}