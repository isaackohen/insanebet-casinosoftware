<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Utils\APIResponse;
use Illuminate\Http\Request;

class LogoutController
{
    public function logout()
    {
        auth()->logout();

        return APIResponse::success();
    }
}
