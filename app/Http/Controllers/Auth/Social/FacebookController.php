<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Auth\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class FacebookController
{
    public function facebook(Request $request)
    {
        $redirect_uri = url('/');
        $client_id = \App\Settings::get('fb_client_id');
        $client_secret = \App\Settings::get('fb_client_secret');

        if (! is_null($request->code)) {
            $url = 'https://graph.facebook.com/v3.2/oauth/access_token';
            $params = [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'redirect_uri' => $redirect_uri.'/auth/fb',
                'code' => $request->code,
                'scope' => 'email',
            ];

            $obj = json_decode(file_get_contents($url.'?'.urldecode(http_build_query($params))));
            if (isset($obj->access_token)) {
                $userInfo = json_decode(file_get_contents('https://graph.facebook.com/v3.2/me?fields=id,name,email&access_token='.$obj->access_token), true);

                if (isset($userInfo['id'])) {
                    $user_id = $userInfo['id'];

                    if (auth('sanctum')->guest()) {
                        $user = User::where('fb', $user_id)->first();
                        if (is_null($user)) {
                            Helper::createUser(null, $userInfo['name'], null, $userInfo['picture'] ?? null, ['fb' => $user_id]);
                        } else {
                            $user->update([
                                'login_ip' => User::getIp(),
                                'login_multiaccount_hash' => $request->hasCookie('s') ? $request->cookie('s') : null,
                                'tfa_persistent_key' => null,
                                'tfa_onetime_key' => null,
                            ]);
                            auth('sanctum')->login($user, true);
                        }

                        return redirect()->to('/');
                    } else {
                        if (User::where('fb', $user_id)->first() != null) {
                            return __('general.profile.somebody_already_linked');
                        }
                        auth('sanctum')->user()->update([
                            'fb' => $user_id,
                        ]);

                        return redirect('/user/'.auth('sanctum')->user()->_id.'#settings');
                    }
                } else {
                    return json_encode(['error' => 'user id is not granted']);
                }
            } else {
                return json_encode(['error' => 'access_token is not granted']);
            }
        } else {
            return redirect()->to('https://www.facebook.com/v3.2/dialog/oauth?'.urldecode(http_build_query([
                'client_id' => $client_id,
                'redirect_uri' => $redirect_uri.'/auth/fb',
                'response_type' => 'code',
                'state' => '{st=xbnf52l,ds=731562}',
                'scope' => 'email',
            ])));
        }
    }
}
