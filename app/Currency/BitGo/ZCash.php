<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class ZCash extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_zec';
    }

    public function walletId(): string
    {
        return 'zec';
    }

    public function name(): string
    {
        return 'ZEC';
    }

    public function alias(): string
    {
        return 'zcash';
    }

    public function displayName(): string
    {
        return 'ZCash';
    }

    public function icon(): string
    {
        return 'zec';
    }

    public function style(): string
    {
        return 'white';
    }

    public function getCurrencyCode()
    {
        return config('app.debug') ? CurrencyCode::ZCASH_TESTNET : CurrencyCode::ZCASH;
    }
}
