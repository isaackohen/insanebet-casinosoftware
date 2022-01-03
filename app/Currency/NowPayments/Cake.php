<?php

namespace App\Currency\NowPayments;

class Cake extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_cake';
    }

    public function walletId(): string
    {
        return 'cake';
    }

    public function name(): string
    {
        return 'CAKE';
    }

    public function alias(): string
    {
        return 'cake';
    }

    public function conversionID(): string
    {
        return 'pancakeswap-token';
    }

    public function displayName(): string
    {
        return 'CAKE';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }

    public function icon(): string
    {
        return 'cake';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/CAKE.png?w=64&h=64';
    }
    public function nowpayments(): string
    {
        return 'CAKE';
    }
}
