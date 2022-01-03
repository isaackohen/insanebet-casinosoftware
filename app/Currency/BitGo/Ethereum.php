<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Ethereum extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_eth';
    }

    public function walletId(): string
    {
        return 'eth';
    }

    public function name(): string
    {
        return 'ETH';
    }

    public function alias(): string
    {
        return 'ethereum';
    }

    public function displayName(): string
    {
        return 'Ethereum';
    }

    public function icon(): string
    {
        return 'eth';
    }

    public function style(): string
    {
        return '#627eea';
    }

    public function getCurrencyCode()
    {
        return config('app.debug') ? CurrencyCode::ETHEREUM_TESTNET : CurrencyCode::ETHEREUM;
    }
}
