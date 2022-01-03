<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Litecoin extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_ltc';
    }

    public function walletId(): string
    {
        return 'ltc';
    }

    public function name(): string
    {
        return 'LTC';
    }

    public function alias(): string
    {
        return 'litecoin';
    }

    public function displayName(): string
    {
        return 'Litecoin';
    }

    public function icon(): string
    {
        return 'ltc';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }

    public function getCurrencyCode()
    {
        return config('app.debug') ? CurrencyCode::LITECOIN_TESTNET : CurrencyCode::LITECOIN;
    }
}
