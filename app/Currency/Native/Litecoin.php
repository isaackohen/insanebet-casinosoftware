<?php

namespace App\Currency\Native;

class Litecoin extends V16RPCBitcoin
{
    public function id(): string
    {
        return 'native_ltc';
    }

    public function walletId(): string
    {
        return 'ltc';
    }

    public function name(): string
    {
        return 'LTC';
    }

    public function alias(): string
    {
        return 'litecoin';
    }

    public function displayName(): string
    {
        return 'Litecoin';
    }

    public function icon(): string
    {
        return 'ltc';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }
}
