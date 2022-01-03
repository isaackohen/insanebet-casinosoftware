<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class BitcoinCash extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_bch';
    }

    public function walletId(): string
    {
        return 'bch';
    }

    public function name(): string
    {
        return 'BCH';
    }

    public function alias(): string
    {
        return 'bitcoin-cash';
    }

    public function displayName(): string
    {
        return 'Bitcoin Cash';
    }

    public function icon(): string
    {
        return 'bch';
    }

    public function style(): string
    {
        return '#8dc351';
    }

    public function getCurrencyCode()
    {
        return config('app.debug') ? CurrencyCode::BITCOIN_CASH_TESTNET : CurrencyCode::BITCOIN_CASH;
    }
}
