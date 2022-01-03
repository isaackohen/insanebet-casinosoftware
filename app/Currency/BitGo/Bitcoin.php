<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Bitcoin extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_btc';
    }

    public function walletId(): string
    {
        return 'btc';
    }

    public function name(): string
    {
        return 'BTC';
    }

    public function alias(): string
    {
        return 'bitcoin';
    }

    public function displayName(): string
    {
        return 'Bitcoin';
    }

    public function icon(): string
    {
        return 'btc-icon';
    }

    public function style(): string
    {
        return '#f7931a';
    }

    public function getCurrencyCode()
    {
        return config('app.debug') ? CurrencyCode::BITCOIN_TESTNET : CurrencyCode::BITCOIN;
    }
}
