<?php

namespace App\Currency\Local;

use App\Currency\Currency;

/**
 * Not a cryptocurrency, used alongside with a Aggregator to process payments
 */
abstract class LocalCurrency extends Currency
{
    public function style(): string
    {
        return 'lightgray';
    }

    public function newWalletAddress(): string
    {
        return 'Unsupported operation';
    }

    public function isRunning(): bool
    {
        return true;
    }

    public function process(string $wallet = null)
    {
    }

    public function send(string $from, string $to, float $sum)
    {
    }

    public function depositmethod(): string
    {
        return 'local';
    }

    public function withdrawmethod(): string
    {
        return 'local';
    }

    public function nowpayments()
    {
        return null;
    }

    public function setupWallet()
    {
    }

    public function coldWalletBalance(): float
    {
        return -1;
    }

    public function hotWalletBalance(): float
    {
        return -1;
    }

    public function minBet(): float
    {
        return 0.01;
    }
}
