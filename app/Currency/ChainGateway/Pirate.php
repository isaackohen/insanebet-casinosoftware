<?php

namespace App\Currency\СhainGateway;

use Illuminate\Support\Facades\Cache;

class Pirate extends СhainGatewaySupport
{
    public function id(): string
    {
        return 'cg_pirate';
    }

    public function walletId(): string
    {
        return 'pirate';
    }

    public function name(): string
    {
        return 'PIRATE';
    }

    public function alias(): string
    {
        return 'pirate';
    }

    public function conversionID(): string
    {
        return 'pirate';
    }

    public function displayName(): string
    {
        return 'PIRATE';
    }

    public function style(): string
    {
        return '#4a90e2';
    }

    public function tokenPrice()
    {
        $result = Cache::remember('pirate', 180, function () {
            return json_decode(file_get_contents('https://api.autoshark.info/api/tokens/0x63041a8770c4cfe8193d784f3dc7826eab5b7fd2'));
        });

        return $result->data->price;
    }
}
