<?php

namespace App\Http\Controllers\Admin;

use App\GlobalNotification;
use App\Http\Controllers\Controller;
use App\Notifications\BrowserOnlyNotification;
use App\Notifications\CustomNotification;
use App\User;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class NotificationsController extends Controller
{
    public function browser(Request $request)
    {
        Notification::send(User::where('notification_bonus', true)->get(),
            new BrowserOnlyNotification(request('title'), request('message')));

        return APIResponse::success();
    }

    public function standalone(Request $request)
    {
        Notification::send(User::get(), new CustomNotification(request('title'), request('message')));

        return APIResponse::success();
    }

    public function global(Request $request)
    {
        GlobalNotification::create([
            'icon' => request('icon'),
            'text' => request('text'),
            'type' => request('type'),
        ]);
        (new \App\ActivityLog\GlobalNotificationLog())->insert(['state' => true, 'text' => request('text'), 'icon' => request('icon')]);

        return APIResponse::success();
    }

    public function globalRemove(Request $request)
    {
        $n = GlobalNotification::where('_id', request('id'));
        (new \App\ActivityLog\GlobalNotificationLog())->insert(['state' => false, 'text' => $n->first()->text, 'icon' => $n->first()->icon]);
        $n->delete();

        return APIResponse::success();
    }

    public function notificationsData(Request $request)
    {
        return APIResponse::success([
            'subscribers' => User::where('notification_bonus', true)->count(),
            'global' => GlobalNotification::get()->toArray(),
        ]);
    }
}
