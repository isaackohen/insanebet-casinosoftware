<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Auth\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DiscordController
{
    public function discord(Request $request)
    {
        $redirect_uri = url('/');
        $client_id = \App\Settings::get('name', 'discord_client_id');
        $client_secret = \App\Settings::get('discord_client_secret');

        if (! is_null($request->code)) {
            $url = 'https://discord.com/api/v6/oauth2/token';
            $params = [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'grant_type' => 'authorization_code',
                'code' => $request->code,
                'scope' => 'identify',
                'redirect_uri' => "$redirect_uri/auth/discord",
            ];

            $obj = json_decode(Helper::curl($url, $params));
            if (isset($obj->access_token)) {
                $info = Helper::apiRequest('https://discord.com/api/users/@me', $obj->access_token);

                if (auth('sanctum')->guest()) {
                    $user = User::where('discord', $info->id)->first();
                    if (is_null($user)) {
                        Helper::createUser(null, $info->username, null, null, ['discord' => $info->id]);
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
                    if (User::where('discord', $info->id)->first() != null) {
                        return __('general.profile.somebody_already_linked');
                    }
                    auth('sanctum')->user()->update([
                        'discord' => $info->id,
                    ]);

                    return redirect('/user/'.auth('sanctum')->user()->_id.'#settings');
                }
            } else {
                return json_encode(['error' => 'access_token is not granted']);
            }
        } else {
            return redirect()->to("https://discord.com/api/oauth2/authorize?client_id=$client_id&redirect_uri=$redirect_uri/auth/discord&response_type=code&scope=identify");
        }
    }
}
