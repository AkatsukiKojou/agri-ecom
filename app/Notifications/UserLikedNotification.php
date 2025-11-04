<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Profile;

class UserLikedNotification extends Notification
{
    use Queueable;

    public $liker;
    public $profile;

    public function __construct(User $liker, Profile $profile)
    {
        $this->liker = $liker;
        $this->profile = $profile;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "{$this->liker->name} liked your shop profile.",
            'liker_id' => $this->liker->id,
            'profile_id' => $this->profile->id,
        ];
    }
}