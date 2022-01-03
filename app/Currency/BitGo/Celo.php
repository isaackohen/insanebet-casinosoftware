<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Celo extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_celo';
    }

    public function walletId(): string
    {
        return 'celo';
    }

    public function name(): string
    {
        return 'CELO';
    }

    public function alias(): string
    {
        return 'celo-dollar';
    }

    public function displayName(): string
    {
        return 'Celo';
    }

    public function icon(): string
    {
        return 'celo';
    }

    public function style(): string
    {
        return '#35d07f';
    }

    public function getCurrencyCode()
    {
        return 'celo';
    }
}
