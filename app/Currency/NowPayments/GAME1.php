<?php

namespace App\Currency\NowPayments;

class GAME1 extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_game1';
    }

    public function walletId(): string
    {
        return 'game1';
    }

    public function name(): string
    {
        return 'GAME1';
    }

    public function alias(): string
    {
        return 'game1';
    }

    public function conversionID(): string
    {
        return 'game1network';
    }

    public function displayName(): string
    {
        return 'GAME1';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }
    public function iconId(): string
    {
        return 'https://s2.coinmarketcap.com/static/img/coins/64x64/12384.png';
    }
    
    public function tatumAccountID(): string
    {
        return env('TATUM_ACCOUNTID_GAME1');
    }

    public function nowpayments(): string
    {
        return 'tatum';
    }
}
