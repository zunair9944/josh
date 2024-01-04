<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationsController extends Controller
{
    public function get_user_notifications(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::where('for', $user->id)->get();
        return response()->json(['notifications' => $notifications],200);

    }
}
