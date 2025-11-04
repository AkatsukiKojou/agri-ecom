<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserFollowedProfile extends Notification
{
    use Queueable;

    public $user, $profile;

    public function __construct($user, $profile)
    {
        $this->user = $user;
        $this->profile = $profile;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'follow',
            'message' => "{$this->user->name} followed your profile: {$this->profile->farm_name}",
            'user_id' => $this->user->id,
            'profile_id' => $this->profile->id,
        ];
    }
}