<?php

namespace App\Currency\NowPayments;

class Ethereum extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_eth';
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

    public function conversionID(): string
    {
        return 'ethereum';
    }

    public function displayName(): string
    {
        return 'Ethereum';
    }

    public function style(): string
    {
        return '#627eea';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/ETH.png?w=64&h=64';
    }
    public function nowpayments(): string
    {
        return 'eth';
    }
}
