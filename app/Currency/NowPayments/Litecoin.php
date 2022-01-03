<?php

namespace App\Currency\NowPayments;

class Litecoin extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_ltc';
    }

    public function walletId(): string
    {
        return 'ltc';
    }

    public function name(): string
    {
        return 'LTC';
    }

    public function alias(): string
    {
        return 'litecoin';
    }

    public function conversionID(): string
    {
        return 'litecoin';
    }

    public function displayName(): string
    {
        return 'Litecoin';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }

    public function icon(): string
    {
        return 'ltc';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/LTC.png?w=64&h=64';
    }
    public function nowpayments(): string
    {
        return 'ltc';
    }
}
