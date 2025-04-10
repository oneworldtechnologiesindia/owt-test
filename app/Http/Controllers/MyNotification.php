<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class MyNotification extends Controller
{
    public function getNotification(Request $request)
    {
        $loginUser = Auth::user();
        // $notifications = $loginUser->unreadNotifications;
        $notifications = Notification::where(['notifiable_id' => $loginUser->id, 'notifiable_type' => "App\User"])->whereNull('read_at')->get();
        $notificationList = [];
        foreach ($notifications as $notification) {
            // $notificationList[] = [
            //     'title' => $notification->data['title'],
            //     'body' => $notification->data['body'],
            //     'type' => $notification->data['type'],
            //     'url' => $notification->data['url'],
            //     'icon' => $notification->data['type'] == 'appointment' ? 'bx bx-calendar' : 'bx bx-cart',
            //     'time' => str_ireplace(
            //         [' seconds', ' second', ' minutes', ' minute', ' hours', ' hour', ' days', ' day', ' weeks', ' week'],
            //         [' sec', ' sec', ' mins', ' min', ' hrs', ' hr', ' days', ' day', ' weeks', ' week'],
            //         $notification->created_at->diffForHumans()
            //     )
            // ];
            $data = json_decode($notification->data, true);
            $notificationList[] = [
                'title' => $data['title'],
                'body' => $data['body'],
                'type' => $data['type'],
                'url' => $data['url'],
                'icon' => $data['type'] == 'appointment' ? 'bx bx-calendar' : 'bx bx-cart',
                'time' => str_ireplace(
                    [' seconds', ' second', ' minutes', ' minute', ' hours', ' hour', ' days', ' day', ' weeks', ' week'],
                    [' sec', ' sec', ' mins', ' min', ' hrs', ' hr', ' days', ' day', ' weeks', ' week'],
                    $notification->created_at->diffForHumans()
                )
            ];
        }
        return response()->json(['status' => '200', 'data' => $notificationList], 200);
    }
}
