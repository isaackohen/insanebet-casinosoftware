<?php

namespace App\Http\Controllers\Admin;

use App\Currency\Currency;
use App\Http\Controllers\Controller;
use App\Settings;
use App\Utils\APIResponse;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
    public function currencyOption(Request $request)
    {
        Currency::find(request('currency'))->option(request('option'), request('value'));

        return APIResponse::success();
    }

    public function currencySettings()
    {
        $foundEmpty = false;
        $foundCount = 0;

        $options = [];

        foreach (Currency::getAllSupportedCoins() as $currency) {
            if ($currency->option('withdraw_address') === '' || $currency->option('transfer_address') === '' || $currency->option('withdraw_address') === '1' || $currency->option('transfer_address') === '1') {
                $foundEmpty = true;
                $foundCount++;
            }

            $currencyOptions = [];

            foreach ($currency->getOptions() as $option) {
                array_push($currencyOptions, [
                    'id' => $option->id(),
                    'readOnly' => $option->readOnly(),
                    'value' => $currency->option($option->id()),
                    'name' => $option->name(),
                ]);
            }

            $options = array_merge($options, [
                $currency->id() => $currencyOptions,
            ]);
        }

        return APIResponse::success([
            'foundEmpty' => $foundEmpty,
            'foundCount' => $foundCount,
            'options' => $options,
            'coins' => Currency::toCurrencyArray(Currency::getAllSupportedCoins()),
        ]);
    }

    public function toggleCurrency(Request $request)
    {
        $currenciesJson = json_decode(Settings::get('currencies', '["native_btc"]', true));
        $currencies = [];
        \Illuminate\Support\Facades\Log::info($currenciesJson);

        foreach ($currenciesJson as $id) {
            $currency = Currency::find($id);
            if ($currency->walletId() == $request->walletId) {
                continue;
            }
            array_push($currencies, $currency->id());
        }

        if ($request->type !== 'disabled') {
            array_push($currencies, $request->type.'_'.$request->walletId);
        }

        Settings::set('currencies', json_encode($currencies));

        return APIResponse::success();
    }

    public function currencyBalance()
    {
        $balance = [];

        foreach (Currency::all() as $currency) {
            $cold = $currency->coldWalletBalance();
            $hot = $currency->hotWalletBalance();

            $balance = array_merge($balance, [
                $currency->id() => [
                    'status' => $currency->isRunning(),
                    'deposit' => $cold,
                    'withdraw' => $hot,
                ],
            ]);
        }

        return APIResponse::success($balance);
    }
}
