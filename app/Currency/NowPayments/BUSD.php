<?php

namespace App\Currency\NowPayments;

class BUSD extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_busd';
    }

    public function walletId(): string
    {
        return 'busd';
    }

    public function name(): string
    {
        return 'BUSD';
    }

    public function alias(): string
    {
        return 'busd';
    }

    public function conversionID(): string
    {
        return 'binance-usd';
    }

    public function displayName(): string
    {
        return 'BUSD';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }

    public function icon(): string
    {
        return 'busd';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/BUSD.png?w=64&h=64';
    }

    public function tatumAccountID(): string
    {
        return env('TATUM_ACCOUNTID_BUSDBSC');
    }

    public function nowpayments(): string
    {
        return 'BUSDBSC';
        //return 'tatum';
    }
}
