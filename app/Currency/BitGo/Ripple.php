<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Ripple extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_xrp';
    }

    public function walletId(): string
    {
        return 'xrp';
    }

    public function name(): string
    {
        return 'XRP';
    }

    public function alias(): string
    {
        return 'ripple';
    }

    public function displayName(): string
    {
        return 'Ripple';
    }

    public function icon(): string
    {
        return 'xrp';
    }

    public function style(): string
    {
        return 'white';
    }

    public function getCurrencyCode()
    {
        return config('app.debug') ? CurrencyCode::RIPPLE_TESTNET : CurrencyCode::RIPPLE;
    }
}
