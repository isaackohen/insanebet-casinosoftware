<?php

namespace App\Currency\СhainGateway;
use App\Currency\ChainGateway\ChainGatewaySupport;


class RXC extends СhainGatewaySupport
{
    public function id(): string
    {
        return 'cg_rxc';
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

    public function icon(): string
    {
        return 'rxc';
    }
    public function iconId(): string
    {
        return 'https://games.cdn4.dk/currencyicons_3d_3/CAKE.png?w=64&h=64';
    }
    public function nowpayments(): string
    {
        return 'RXC';
    }
}
