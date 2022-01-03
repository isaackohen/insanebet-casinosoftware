<?php

namespace App\Http\Controllers\Auth\Social;

use App\Http\Controllers\Auth\Helper;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class VkController
{
    public function vk(Request $request)
    {
        $redirect_uri = url('/');
        $client_id = \App\Settings::get('vk_client_id');
        $client_secret = \App\Settings::get('vk_client_secret');

        if (! is_null($request->code)) {
            $obj = json_decode(Helper::curl('https://oauth.vk.com/access_token?client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.$redirect_uri.'/auth/vk&code='.$request->code));

            if (isset($obj->access_token)) {
                $info = json_decode(Helper::curl('https://api.vk.com/method/users.get?fields=photo_200&access_token='.$obj->access_token.'&v=5.103'), true);

                if (auth('sanctum')->guest()) {
                    $photo = array_key_exists('photo_200', $info['response'][0]) ? $info['response'][0]['photo_200'] : null;
                    $user = User::where('vk', $info['response'][0]['id'])->first();
                    if (is_null($user)) {
                        Helper::createUser(null, $info['response'][0]['first_name'].' '.$info['response'][0]['last_name'], null, $photo, ['vk' => $info['response'][0]['id']]);
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
                    $id = $info['response'][0]['id'];
                    if (User::where('vk', $id)->first() != null) {
                        return __('general.profile.somebody_already_linked');
                    }
                    auth('sanctum')->user()->update(['vk' => $id]);

                    return redirect('/user/'.auth('sanctum')->user()->_id.'#settings');
                }
            }

            return redirect()->to('/');
        } else {
            return redirect()->to('https://oauth.vk.com/authorize?client_id='.$client_id.'&display=page&redirect_uri='.$redirect_uri.'/auth/vk&scope=photos&response_type=code&v=5.53');
        }
    }
}
