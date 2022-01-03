<?php

namespace App\Currency\NowPayments;

class RXCG extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_rxcg';
    }

    public function walletId(): string
    {
        return 'rxcg';
    }

    public function name(): string
    {
        return 'RXCG';
    }

    public function alias(): string
    {
        return 'rxcg';
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
        return 'RXCG';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/RXCG.png?w=64&h=64';
    }
    
    public function icon(): string
    {
        return 'RXCG';
    }

    public function tatumAccountID(): string
    {
        return env('TATUM_ACCOUNTID_RXCG');
    }

    public function nowpayments(): string
    {
        return 'RXCG';
    }
}


 