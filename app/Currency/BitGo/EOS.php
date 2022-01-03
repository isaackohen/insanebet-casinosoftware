<?php

namespace App\Currency\BitGo;

use neto737\BitGoSDK\Enum\CurrencyCode;

class EOS extends BitGoCurrency
{
    public function id(): string
    {
        return 'bg_eos';
    }

    public function walletId(): string
    {
        return 'eos';
    }

    public function name(): string
    {
        return 'EOS';
    }

    public function alias(): string
    {
        return 'eos';
    }

    public function displayName(): string
    {
        return 'EOS';
    }

    public function icon(): string
    {
        return 'eos';
    }

    public function style(): string
    {
        return 'white';
    }

    public function getCurrencyCode()
    {
        return CurrencyCode::EOS;
    }
}
