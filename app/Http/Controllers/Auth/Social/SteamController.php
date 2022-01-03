<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Auth\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class SteamController
{
    public function steam(Request $request)
    {
        $redirect_uri = url('/');
        $openid = new LightOpenID(url('/'));
        if (! $openid->mode) {
            $openid->identity = 'http://steamcommunity.com/openid';

            return redirect()->to($openid->authUrl());
        } elseif ($openid->mode == 'cancel') {
            redirect()->to('/');
        } else {
            if ($openid->validate()) {
                $id = $openid->identity;

                $key = \App\Settings::get('steam_web_api_key');
                $response = json_decode(file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$key.'&steamids='.substr($id, strrpos($id, '/') + 1, strlen($id))), true)['response'];

                if (auth('sanctum')->guest()) {
                    $user = User::where('steam', $response['players'][0]['steamid'])->first();
                    if (is_null($user)) {
                        Helper::createUser(null, $response['players'][0]['personaname'], null, null, ['steam' => $response['players'][0]['steamid']]);
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
                    if (User::where('steam', $response['players'][0]['steamid'])->first() != null) {
                        return __('general.profile.somebody_already_linked');
                    }
                    auth('sanctum')->user()->update([
                        'steam' => $response['players'][0]['steamid'],
                    ]);

                    return redirect('/user/'.auth('sanctum')->user()->_id.'#settings');
                }
            }
        }
    }
}
