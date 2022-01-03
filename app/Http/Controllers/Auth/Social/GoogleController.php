<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Auth\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class GoogleController
{
    public function google(Request $request)
    {
        $redirect_uri = url('/');
        $client_id = \App\Settings::get('google_client_id');
        $client_secret = \App\Settings::get('google_client_secret');

        if (! is_null($request->code)) {
            $url = 'https://accounts.google.com/o/oauth2/token';
            $params = [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri.'/auth/google',
                'grant_type' => 'authorization_code',
                'code' => $request->code,
            ];

            $obj = json_decode(Helper::curl($url, $params));
            if (isset($obj->access_token)) {
                $params['access_token'] = $obj->access_token;
                $userInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo'.'?'.urldecode(http_build_query($params))), true);

                if (isset($userInfo['id'])) {
                    $user_id = $userInfo['id'];

                    if (auth('sanctum')->guest()) {
                        $user = User::where('google', $user_id)->first();
                        if (is_null($user)) {
                            Helper::createUser(null, $userInfo['name'], null, $userInfo['avatar'] ?? null, ['google' => $user_id]);
                        } else {
                            $user->update([
                                'login_ip' => User::getIp(),
                                'login_multiaccount_hash' => $request->hasCookie('s') ? $request->cookie('s') : null,
                                'tfa_persistent_key' => null,
                                'tfa_onetime_key' => null,
                            ]);
                            auth('sanctum')->login($user, true);
                        }
                    } else {
                        if (User::where('google', $user_id)->first() != null) {
                            return __('general.profile.somebody_already_linked');
                        }
                        auth('sanctum')->user()->update([
                            'google' => $user_id,
                        ]);

                        return redirect('/user/'.auth('sanctum')->user()->_id.'#settings');
                    }

                    return redirect()->to('/');
                } else {
                    return json_encode(['error' => 'user id is not granted']);
                }
            } else {
                return json_encode(['error' => 'access_token is not granted']);
            }
        } else {
            return redirect()->to('https://accounts.google.com/o/oauth2/auth?'.urldecode(http_build_query([
                'redirect_uri' => $redirect_uri.'/auth/google',
                'response_type' => 'code',
                'client_id' => $client_id,
                'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
            ])));
        }
    }
}
