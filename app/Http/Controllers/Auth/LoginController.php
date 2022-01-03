<?php

namespace App\Http\Controllers\Auth;

use App\Games\Kernel\ProvablyFair;
use App\Http\Controllers\Auth\Helper;
use App\Http\Requests\Auth\LoginLoginRequest;
use App\Mail\ResetPassword;
use App\PasswordReset;
use App\User;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController
{
    public function login(LoginLoginRequest $request)
    {
        if (! Helper::validateCaptcha($request->captcha)) {
            return APIResponse::reject(2, 'Invalid captcha');
        }

        $user = User::where('email', $request->login)->orWhere('name', $request->login)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return APIResponse::reject(1, 'Wrong credentials');
        }

        $token = $user->createToken()->plainTextToken;
        auth()->login($user, true);

        $user->update([
            'login_ip' => User::getIp(),
            'login_multiaccount_hash' => $request->hasCookie('s') ? $request->cookie('s') : null,
            'tfa_persistent_key' => null,
            'tfa_onetime_key' => null,
        ]);

        return APIResponse::success([
            'user' => $user->toArray(),
            'token' => $token,
        ]);
    }

    public function demologin(Request $request)
    {
        $user = User::where('email', 'demo@david.com')->first();
        $token = $user->createToken()->plainTextToken;
        auth()->login($user, true);

        $user->update([
            'login_ip' => User::getIp(),
            'login_multiaccount_hash' => $request->hasCookie('s') ? $request->cookie('s') : null,
            'tfa_persistent_key' => null,
            'tfa_onetime_key' => null,
        ]);

        return APIResponse::success([
            'user' => $user->toArray(),
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        if ($request->type) {
            if ($request->type === 'validateToken') {
                return PasswordReset::where('user', $request->user)->where('token', $request->token)->first() ? APIResponse::success() : APIResponse::reject(2, 'Invalid token');
            }
            if ($request->type === 'reset') {
                $user = User::where('_id', $request->user)->first();
                if (! $user || PasswordReset::where('user', $request->user)->where('token', $request->token)->first() == null) {
                    return APIResponse::reject(3, 'Invalid token');
                }

                PasswordReset::where('user', $request->user)->where('token', $request->token)->delete();

                $user->update(['password' => Hash::make($request->password)]);

                return APIResponse::success();
            }

            return APIResponse::reject(1, 'Invalid type');
        }

        $user = User::where('email', $request->login)->orWhere('name', $request->login)->first();
        if (! $user) {
            return APIResponse::success();
        }

        $token = ProvablyFair::generateServerSeed();

        PasswordReset::create([
            'user' => $user->_id,
            'token' => $token,
        ]);

        Mail::to($user)->send(new ResetPassword($user->_id, $token));

        return APIResponse::success();
    }

    public function update()
    {
        return APIResponse::success(array_merge(auth('sanctum')->user()->toArray(), [
            'platformVersion' => env('MIX_PLATFORMVERSION'), 
            'rakeback' => auth('sanctum')->user()->rakeback(),
            'vipLevel' => auth('sanctum')->user()->vipLevel(),
            'freespins' => auth('sanctum')->user()->freespins,
        ]));
    }
}
