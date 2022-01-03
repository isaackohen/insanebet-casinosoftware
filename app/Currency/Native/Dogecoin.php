<?php

namespace App\Currency\Native;

class Dogecoin extends V16RPCBitcoin
{
    public function id(): string
    {
        return 'native_doge';
    }

    public function walletId(): string
    {
        return 'doge';
    }

    public function name(): string
    {
        return 'DOGE';
    }

    public function alias(): string
    {
        return 'dogecoin';
    }

    public function displayName(): string
    {
        return 'Dogecoin';
    }

    public function icon(): string
    {
        return 'dogecoin';
    }

    public function style(): string
    {
        return '#c2a633';
    }
}
