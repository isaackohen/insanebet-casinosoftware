<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Dash extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_dash';
    }

    public function walletId(): string
    {
        return 'dash';
    }

    public function name(): string
    {
        return 'DASH';
    }

    public function alias(): string
    {
        return 'dash';
    }

    public function displayName(): string
    {
        return 'Dash';
    }

    public function icon(): string
    {
        return 'dash';
    }

    public function style(): string
    {
        return '#2573c2';
    }

    public function getCurrencyCode()
    {
        return config('app.debug') ? CurrencyCode::DASH_TESTNET : CurrencyCode::DASH;
    }
}
