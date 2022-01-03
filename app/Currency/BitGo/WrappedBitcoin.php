<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class WrappedBitcoin extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_wbtc';
    }

    public function walletId(): string
    {
        return 'wbtc';
    }

    public function name(): string
    {
        return 'WBTC';
    }

    public function alias(): string
    {
        return 'wrapped-bitcoin';
    }

    public function displayName(): string
    {
        return 'Wrapped Bitcoin';
    }

    public function icon(): string
    {
        return 'btc-icon';
    }

    public function style(): string
    {
        return '#eba809';
    }

    public function getCurrencyCode()
    {
        return 'wbtc';
    }
}
