<?php

namespace App\Currency\NowPayments;

class RXC extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_rxc';
    }

    public function walletId(): string
    {
        return 'rxc';
    }

    public function name(): string
    {
        return 'RXC';
    }

    public function alias(): string
    {
        return 'rxc';
    }

    public function tokenContract(): string
    {
        return '0x7c59a57fc16eac270421b74615c4bc009ecd486d';
    }

    public function conversionID(): string
    {
        return 'rxcgames';
    }

    public function displayName(): string
    {
        return 'RXC';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/BNB.png?w=64&h=64';
    }
    
    public function icon(): string
    {
        return 'rxc';
    }

    public function nowpayments(): string
    {
        return 'chaingateway';
    }
}


