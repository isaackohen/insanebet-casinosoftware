<?php

namespace App\Currency\NowPayments;

class BNB extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_bnb';
    }

    public function walletId(): string
    {
        return 'bnb';
    }

    public function name(): string
    {
        return 'BNB';
    }

    public function alias(): string
    {
        return 'bnb';
    }

    public function conversionID(): string
    {
        return 'binancecoin';
    }

    public function displayName(): string
    {
        return 'BNB';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/BNB.png?w=64&h=64';
    }
    
    public function tatumAccountID(): string
    {
        return env('TATUM_ACCOUNTID_BNBBSC');
    }

    public function nowpayments(): string
    {
        return 'BNBBSC';
        //return 'tatum';
    }
}
