<?php

namespace App\Http\Controllers;

use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class MainController extends Controller
{
    public function main()
    {
        return view('layouts.app');
    }

    public function avatar(string $hash)
    {
        $size = 100;
        $icon = new Jdenticon\Identicon();
        $icon->setValue($hash);
        $icon->setSize($size);
        $style = new Jdenticon\IdenticonStyle();
        $style->setBackgroundColor('#21232a');
        $icon->setStyle($style);
        $icon->displayImage('png');

        return response()->noContent(200)->header('Content-Type', 'image/png');
    }

    //Broadcasting auth

    public function broadcasting(Request $request)
    {
        $user = auth('sanctum')->guest() ? new GenericUser(['_id' => microtime()]) : auth('sanctum')->user();
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return Broadcast::auth(request());
    }
}
