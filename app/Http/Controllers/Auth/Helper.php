<?php

namespace App\Http\Controllers\Auth;

use App\Games\Kernel\ProvablyFair;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class Helper
{
    public static function validateCaptcha($payload): bool
    {
        return true;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'secret' => \App\Settings::get('recaptcha_secret_key'),
            'response' => $payload,
            'remoteip' => User::getIp(),
        ]);
        $data = json_decode(curl_exec($ch), true); 
        curl_close($ch);

        return $data['success'];
    }

    public static function createUser($email, $login, $password, $avatar = null, $additionalData = [])
    {

        
        $bgAvatar = env('MIX_APP_DEFAULT_AVATAR_BG');
        $avatar = 'https://avatars.dicebear.com/api/initials/'.$login.'.svg?background=%23'.$bgAvatar;
        $user = User::create(array_merge([
            'name' => $login,
            'password' => $password == null ? null : Hash::make($password),
            'avatar' => $avatar ?? '/avatar/'.uniqid(),
            'email' => $email,
            'client_seed' => ProvablyFair::generateServerSeed(),
            'access' => 'user',
            'name_history' => [['time' => Carbon::now(), 'name' => $login]],
            'register_ip' => User::getIp(),
            'login_ip' => User::getIp(),
            'freespins' => 0,
            'register_multiaccount_hash' => request()->hasCookie('s') ? request()->cookie('s') : null,
            'login_multiaccount_hash' => request()->hasCookie('s') ? request()->cookie('s') : null,
        ], $additionalData));

        if (isset($_COOKIE['c'])) {
            $referrer = User::where('name', $_COOKIE['c'])->first();
            if ($referrer != null) {
                $user->update(['referral' => $referrer->_id]);
                //$user->balance(Currency::all()[0])->add(floatval(Currency::all()[0]->option('referral_bonus')));
            }
        }

        auth()->login($user, true);

        return $user;
    }

    public static function curl($url, $params = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        if ($params != null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
        }
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    public static function apiRequest($url, $access_token, $auth = 'Bearer', $post = false, $headers = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($post === 'PUT') {
            curl_setopt($ch, CURLOPT_PUT, true);
        } elseif ($post) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }

        $headers[] = 'Accept: application/json';

        $headers[] = 'Authorization: '.$auth.' '.$access_token;

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        return json_decode($response);
    }
}
