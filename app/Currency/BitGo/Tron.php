<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class Tron extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_trx';
    }

    public function walletId(): string
    {
        return 'trx';
    }

    public function name(): string
    {
        return 'TRX';
    }

    public function alias(): string
    {
        return 'tron';
    }

    public function displayName(): string
    {
        return 'Tron (TRX)';
    }

    public function icon(): string
    {
        return 'trx';
    }

    public function style(): string
    {
        return '#eb0a29';
    }

    public function getCurrencyCode()
    {
        return 'trx';
    }
}
