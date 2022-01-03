<?php

namespace App\Console\Commands;

use App\Chat;
use App\Currency\Currency;
use App\Invoice;
use App\Leaderboard;
use App\Settings;
use App\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\Decimal128;

class PullingWallet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datagamble:pullingwallet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls marked wallets';

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
        foreach (\App\Invoice::where('pull_state', '1')->get() as $invoice) {
            try {
                $currency = Currency::find($invoice->currency);

                if ($currency->contractaddress() === '0') {
                    $invoice->update(['pull_state' => '3']);

                    return success();
                } else {
                    $url = 'https://eu.bsc.chaingateway.io/v1/sendBinancecoin';
                }
                $apikey = config('settings.chaingateway_apikey'); // API Key in your account panel
            $password = config('settings.chaingateway_password'); // Chaingateway password
            $to = $invoice->ledger;
                $from = $currency->fundaddress(); // WHere to send towards
            $amount = config('settings.chaingateway_bnbtxfund'); //0.002315
            // Define function endpoint
            $ch = curl_init($url);

                // Setup request to send json via POST. This is where all parameters should be entered.

                $payload = json_encode(['from' => $from, 'to' => $to, 'password' => $password, 'amount' => $amount]);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: '.$apikey]);

                // Return response instead of printing.
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Send request.
                $result = curl_exec($ch);
                curl_close($ch);

                // Decode the received JSON string
                $resultdecoded = json_decode($result, true);

                $okresponse = $resultdecoded['ok'];
                if ($okresponse === true) {
                    $invoice->update(['pull_state' => '2']);

                    return success();
                }
            } catch (\Exception $e) {
            }
            sleep(1);
        }

        foreach (\App\Invoice::where('pull_state', '2')->get() as $invoice) {
            try {
                $currency = Currency::find($invoice->currency);

                if ($currency->contractaddress() === '0') {
                    $url = 'https://eu.bsc.chaingateway.io/v1/sendBinancecoin';
                } else {
                    $url = 'https://eu.bsc.chaingateway.io/v1/sendToken';
                }
                $apikey = config('settings.chaingateway_apikey'); // API Key in your account panel
            $password = config('settings.chaingateway_password'); // Chaingateway password
            $to = config('settings.chaingateway_endaddress'); // WHere to send towards

            // Define function endpoint
                $ch = curl_init($url);

                // Setup request to send json via POST. This is where all parameters should be entered.

                if ($currency->contractaddress() === '0') {
                    $payload = json_encode(['from' => $invoice->ledger, 'to' => $to, 'password' => $password, 'amount' => $invoice->sum]);
                } else {
                    $payload = json_encode(['contractaddress' => $currency->contractaddress(), 'from' => $invoice->ledger, 'to' => $to, 'password' => $password, 'amount' => $invoice->sum]);
                }

                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: '.$apikey]);

                // Return response instead of printing.
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Send request.
                $result = curl_exec($ch);
                Log::notice($result);
                curl_close($ch);

                // Decode the received JSON string
                $resultdecoded = json_decode($result, true);

                $okresponse = $resultdecoded['ok'];
                if ($okresponse === true) {
                    $invoice->update(['pull_state' => '3']);
                    $invoice->update(['pull_txid' => $resultdecoded['txid']]);

                    return success();
                }
            } catch (\Exception $e) {
            }
            sleep(1);
        }

        foreach (\App\Invoice::where('pull_state', '3')->get() as $invoice) {
            try {
                $currency = Currency::find($invoice->currency);

                $url = 'https://eu.bsc.chaingateway.io/v1/clearAddress';
                $apikey = config('settings.chaingateway_apikey'); // API Key in your account panel
            $password = config('settings.chaingateway_password'); // Chaingateway password
            $to = config('settings.chaingateway_endaddress'); // WHere to send towards

            // Define function endpoint
                $ch = curl_init($url);

                // Setup request to send json via POST. This is where all parameters should be entered.

                $payload = json_encode(['binancecoinaddress' => $invoice->ledger, 'newaddress' => $to, 'password' => $password]);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: '.$apikey]);

                // Return response instead of printing.
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Send request.
                $result = curl_exec($ch);
                curl_close($ch);

                // Decode the received JSON string
                $resultdecoded = json_decode($result, true);

                $okresponse = $resultdecoded['ok'];
                if ($okresponse === true) {
                    $invoice->update(['pull_state' => '4']);
                    if ($currency->contractaddress() === '0') {
                        $invoice->update(['pull_txid' => $resultdecoded['txid']]);
                    }

                    return success();
                }
            } catch (\Exception $e) {
            }
            sleep(1);
        }
    }
}
