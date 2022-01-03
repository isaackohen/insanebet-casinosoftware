<?php

namespace App\Http\Controllers\Api;

use App\Utils\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController
{
    public function mark(Request $request)
    {
        auth('sanctum')->user()->notifications()->where('id', $request->id)->first()->markAsRead();

        return APIResponse::success();
    }

    public function unread(Request $request)
    {
        return APIResponse::success([
            'notifications' => auth('sanctum')->user()->unreadNotifications()->get()->toArray(),
        ]);
    }
}
