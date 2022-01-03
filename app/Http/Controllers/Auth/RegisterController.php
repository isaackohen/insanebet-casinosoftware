<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Helper;
use App\Http\Requests\Auth\RegisterRegisterRequest;
use App\User;
use App\Utils\APIResponse;
use Illuminate\Http\Request;

class RegisterController
{
    public function register(RegisterRegisterRequest $request)
    {
        if (! Helper::validateCaptcha($request->captcha)) {
            return APIResponse::reject(2, 'Invalid captcha');
        }

        $user = Helper::createUser($request->email, $request->name, $request->password);

        return APIResponse::success([
            'user' => $user->toArray(),
            'token' => $user->createToken()->plainTextToken,
        ]);
    }
}
