<?php

namespace App\Currency\Local;

use App\Currency\Option\WalletOption;
use Illuminate\Support\Facades\Cache;

class Inr extends LocalCurrency
{
    public function id(): string
    {
        return 'local_inr';
    }

    public function walletId(): string
    {
        return 'inr';
    }

    public function name(): string
    {
        return 'INR';
    }

    public function alias(): string
    {
        return 'inr';
    }


    public function conversionID(): string
    { 
        return 'INR';
    }

    public function displayName(): string
    {
        return 'INR';
    }

    public function icon(): string
    {
        return 'inr';
    }

    public function style(): string
    {
        return '#bfbbbb';
    }
    public function iconId(): string
    {
        return 'https://cdn2.davidkohen.com/v1/currencyicons_3d_3/INR-2.png?w=64&h=64';
    }

    protected function options(): array
    {
        return [
            new class extends WalletOption {
                public function id()
                {
                    return 'bulkcash_usd';
                }

                public function name(): string
                {
                    return 'INR';
                }
            },
        ];
    }

    public function inrPrice()
    {
        try {
            if (!Cache::has('INRprice')) {
                Cache::put('INRprice', file_get_contents("https://api.coingecko.com/api/v3/coins/{$this->conversionID()}?localization=false&market_data=true"), now()->addHours(1));
            }
            $json = json_decode(Cache::get('INRprice'));


            return number_format((1 / $json->market_data->current_price->inr), 3, '.', '');
        } catch (\Exception $e) {
            try {
                $result = Cache::remember('INRprice', 60, function () {
                    return file_get_contents("https://min-api.cryptocompare.com/data/pricemulti?fsyms={$this->name()}&tsyms=INR");
                });
                $price = json_decode($result, true);



                return number_format((1 / $price[$this->name()]['INR']), 3, '.', '');
            } catch (\Exception $e) {
                //return 0.015;
            }
        }
    }

    public function tokenPrice()
    {

        return 1;
    }
}
