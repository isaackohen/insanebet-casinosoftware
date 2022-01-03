<?php

namespace App\Currency\NowPayments;

class BitcoinCash extends NowPaymentsSupport
{
    public function id(): string
    {
        return 'np_bch';
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

    public function conversionID(): string
    {
        return 'bitcoin-cash';
    }

    public function displayName(): string
    {
        return 'Bitcoin Cash';
    }

    public function style(): string
    {
        return '#8dc351';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/BCH.png?w=64&h=64';
    }
    public function nowpayments(): string
    {
        return 'bch';
    }
}
