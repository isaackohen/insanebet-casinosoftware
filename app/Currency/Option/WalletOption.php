<?php

namespace App\Currency\Option;

abstract class WalletOption
{
    abstract public function id();

    abstract public function name(): string;

    public function readOnly(): bool
    {
        return false;
    }
}
