<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\ProfileFollower;
use App\Models\ProfileLike;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserFollowedAdmin;
use App\Notifications\UserLikedAdmin;

class ProfileActionController extends Controller
{
    public function follow(Profile $profile)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        ProfileFollower::firstOrCreate(['profile_id' => $profile->id, 'user_id' => $user->id]);
        $count = ProfileFollower::where('profile_id', $profile->id)->count();

        // Notify admin/shop owner
        if ($profile->admin) {
            $profile->admin->notify(new UserFollowedAdmin($user, $profile));
        }

        return response()->json(['success' => true, 'followers' => $count]);
    }

    public function unfollow(Profile $profile)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        ProfileFollower::where('profile_id', $profile->id)->where('user_id', $user->id)->delete();
        $count = ProfileFollower::where('profile_id', $profile->id)->count();

        return response()->json(['success' => true, 'followers' => $count]);
    }

    public function like(Profile $profile)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        ProfileLike::firstOrCreate(['profile_id' => $profile->id, 'user_id' => $user->id]);
        $count = ProfileLike::where('profile_id', $profile->id)->count();

        // Notify admin/shop owner
        if ($profile->admin) {
            $profile->admin->notify(new UserLikedAdmin($user, $profile));
        }

        return response()->json(['success' => true, 'likes' => $count]);
    }

    public function unlike(Profile $profile)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['success' => false], 401);

        ProfileLike::where('profile_id', $profile->id)->where('user_id', $user->id)->delete();
        $count = ProfileLike::where('profile_id', $profile->id)->count();

        return response()->json(['success' => true, 'likes' => $count]);
    }
}