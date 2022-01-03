<?php

namespace App\Currency\NowPayments;

class Bitcoin extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_btc';
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

    public function conversionID(): string
    {
        return 'bitcoin';
    }

    public function displayName(): string
    {
        return 'Bitcoin';
    }

    public function style(): string
    {
        return '#f7931a';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/BTC3L.png?w=64&h=64';
    }
    public function nowpayments(): string
    {
        return 'btc';
    }
}
