<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Algorand extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_algo';
    }

    public function walletId(): string
    {
        return 'algo';
    }

    public function name(): string
    {
        return 'ALGO';
    }

    public function alias(): string
    {
        return 'algorand';
    }

    public function displayName(): string
    {
        return 'Algorand';
    }

    public function icon(): string
    {
        return 'algorand';
    }

    public function style(): string
    {
        return 'white';
    }

    public function getCurrencyCode()
    {
        return 'algo';
    }
}
