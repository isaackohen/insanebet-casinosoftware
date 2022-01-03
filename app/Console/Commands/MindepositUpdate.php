<?php

namespace App\Console\Commands;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use App\Events\Deposit;
use App\Http\Controllers\Api\WalletController;
use App\Invoice;
use App\Settings;
use App\Transaction;
use App\User;
use Illuminate\Console\Command;

class MindepositUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:mindepositupdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set min deposit';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Currency::all() as $currency) {
            if ($currency->nowpayments() && $currency->nowpayments() !== 'tatum') {
                $apikey = config('settings.nowpayments_id');
                try {
                    $curlcurrency = curl_init();
                    curl_setopt_array($curlcurrency, [
                        CURLOPT_URL => 'https://api.nowpayments.io/v1/min-amount?currency_from='.$currency->nowpayments().'&currency_to='.$currency->nowpayments(),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => [
                            'x-api-key: '.$apikey.'',
                        ],
                    ]);
                    $responsecurl = curl_exec($curlcurrency);
                    curl_close($curlcurrency);
                    $responseCurrency = json_decode($responsecurl);
                    $mindeposit = $responseCurrency->min_amount;
                    $mindepositusd = $mindeposit * $currency->tokenPrice();
                    $val = 'nowpayments_min_'.$currency->nowpayments();
                    Settings::get($val, 0, true);
                    Settings::where('name', $val)->update(['value' => $mindepositusd]);
                } catch (\Exception $exception) {
                    $this->error($exception);
                }
            }
        }
    }
}
