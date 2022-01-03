<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Tezos extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_xtz';
    }

    public function walletId(): string
    {
        return 'xtz';
    }

    public function name(): string
    {
        return 'XTZ';
    }

    public function alias(): string
    {
        return 'tezos';
    }

    public function displayName(): string
    {
        return 'Tezos';
    }

    public function icon(): string
    {
        return 'xtz';
    }

    public function style(): string
    {
        return '#2c7df7';
    }

    public function getCurrencyCode()
    {
        return 'xtz';
    }
}
