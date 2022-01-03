<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Stellar extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_xlm';
    }

    public function walletId(): string
    {
        return 'xlm';
    }

    public function name(): string
    {
        return 'XLM';
    }

    public function alias(): string
    {
        return 'stellar';
    }

    public function displayName(): string
    {
        return 'Stellar';
    }

    public function icon(): string
    {
        return 'xlm';
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
