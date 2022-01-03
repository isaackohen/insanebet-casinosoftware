<?php

namespace App\Currency\NowPayments;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use App\Invoice;
use App\Settings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

abstract class NowPaymentsSupport extends Currency
{
    private function balance($account): float
    {
        return -1;
    }

    public function coldWalletBalance(): float
    {
        return -1;
    }

    public function hotWalletBalance(): float
    {
        return -1;
    }

    public function isRunning(): bool
    {
        try {
            $json = json_decode(file_get_contents('http://api.nowpayments.io/v1/status'), true);

            return $json['message'] === 'OK';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function send(string $from, string $to, float $sum)
    {
    }

    public function process(string $wallet = null)
    {
    }

    public function processBlock($blockId)
    {
    }

    public function newWalletAddress($accountName = null): string
    { 
        if($this->nowpayments() === 'chaingateway') {
        try {
            $invoice = Invoice::create([
                'currency' => $this->id(),
                'user' => auth('sanctum')->user()->_id,
                'status' => 0,
                'hash' => Hash::make(12),
            ]);
            $apikey = config('settings.chaingateway_apikey');
            $password = config('settings.chaingateway_password');
            $ipn = config('app.url').'/api/callback/chaingateway';
            $ch = curl_init('https://eu.bsc.chaingateway.io/v1/newAddress');
            $payload = json_encode(['password' => $password]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: '.$apikey]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $resultdecoded = json_decode($result, true);
            if ($this->tokenContract() === '0') {
                $this->subcribeToBnb($apikey, $resultdecoded['binancecoinaddress'], $ipn);
            } else {
                Log::notice('token subscribe');
                $test = $this->subcribeToToken($apikey, $this->tokenContract(), $resultdecoded['binancecoinaddress'], $ipn);
                Log::notice($test);
            }
            $invoice->update([
                'ledger' => $resultdecoded['binancecoinaddress'],
                'min_deposit' => '0.01',
                'payid' => uniqid(),
                'min_deposit_usd' => '0.01',
            ]);

            return $resultdecoded['binancecoinaddress'];
        } catch (\Exception $e) {
            Log::critical($e);

            return 'Error';
        }

        } elseif($this->nowpayments() === 'tatum') {

            try {
                $invoice = Invoice::create([
                    'currency' => $this->id(),
                    'user' => auth('sanctum')->user()->_id,
                    'status' => 0,
                    'hash' => Hash::make(12),
                ]);
                $accountid = $this->tatumAccountID();
                $ch = curl_init('https://api.dk.games/tatum/createwallet/'.$accountid.'/'.$invoice->hash);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                $resultdecoded = json_decode($result);

                $invoice->update([
                    'ledger' => $resultdecoded->address,
                    'min_deposit' => '0.01',
                    'payid' => uniqid(),
                    'min_deposit_usd' => '0.01',
                ]);

                return $resultdecoded->address;

            } catch (\Exception $e) {
                Log::critical($e);

                return 'Error';
            }



        } else {

        try {
            $min_deposit_str = 'nowpayments_min_'.$this->nowpayments();
            $min_deposit_usd = round(Settings::where('name', $min_deposit_str)->first()->value + 2, 2);
            $invoice = Invoice::create([
                'currency' => $this->id(),
                'user' => auth('sanctum')->user()->_id,
                'status' => 0,
                'hash' => Hash::make(12),
            ]);
            $ipn = config('app.url').'/api/callback/nowpayments';
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.nowpayments.io/v1/payment',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
				 "price_amount": "'.$min_deposit_usd.'",
				 "price_currency": "usd",
				 "pay_currency": "'.$this->nowpayments().'",
				 "ipn_callback_url": "'.$ipn.'",
				 "order_id": "'.$invoice->_id.'",
				 "order_description": "'.$invoice->hash.'"
			}',
                CURLOPT_HTTPHEADER => [
                    'x-api-key: '.config('settings.nowpayments_id').'',
                    'Content-Type: application/json',
                ],
            ]);
            $response = curl_exec($curl);
            curl_close($curl);
            $responseResult = json_decode($response);
            if ($responseResult->pay_currency == 'xrp' || $responseResult->pay_currency == 'bnb') {
                $invoice->update([
                    'payid' => $responseResult->payin_extra_id,
                    'ledger' => $responseResult->pay_address,
                    'min_deposit' => $responseResult->pay_amount,
                    'min_deposit_usd' => $responseResult->price_amount,
                ]);
            } else {
                $invoice->update([
                    'ledger' => $responseResult->pay_address,
                    'min_deposit' => $responseResult->pay_amount,
                    'payid' => $responseResult->payment_id,
                    'min_deposit_usd' => $responseResult->price_amount,
                ]);
            }

            return $invoice->ledger;
        } catch (\Exception $e) {
            Log::critical($e);

            return 'Error';
        }
    }
    }

    public function setupWallet(): ?string
    {
        return null;
    }

    protected function options(): array
    {
        return [];
    }

    public function depositmethod(): string
    {
        return 'nowpayments';
    }

    public function withdrawmethod(): string
    {
        return 'nowpayments';
    }


    public function subcribeToToken($apikeychaingate, $contactaddy, $address, $urlThings)
    {
        $apikey = $apikeychaingate; // API Key in your account panel
        $contractaddress = $contactaddy; // Contract address of the token you want to watch
        $binancecoinaddress = $address; // Binancecoin address you want to watch
        $url = $urlThings; // URL where you want to receive updates
        // -------------------------------------------------------

        // Define function endpoint
        $ch = curl_init('https://eu.bsc.chaingateway.io/v1/subscribeAddress');

        // Setup request to send json via POST. This is where all parameters should be entered.
        $payload = json_encode(['contractaddress' => $contractaddress, 'binancecoinaddress' => $binancecoinaddress, 'url' => $url]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: '.$apikey]);

        // Return response instead of printing.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Send request.
        $result = curl_exec($ch);
        curl_close($ch);

        // Decode the received JSON string
        $resultdecoded = json_decode($result, true);


        return $resuldecoded;
        Log::notice(json_encode($result));
        Log::notice($url);
        
    }

    public function subcribeToBnb($apikeychaingate, $address, $urlThings)
    {
        $apikey = $apikeychaingate; // API Key in your account panel
        $binancecoinaddress = $address; // Binancecoin address you want to watch
        $url = $urlThings; // URL where you want to receive updates
        // -------------------------------------------------------

        // Define function endpoint
        $ch = curl_init('https://eu.bsc.chaingateway.io/v1/subscribeAddress');

        // Setup request to send json via POST. This is where all parameters should be entered.
        $payload = json_encode(['binancecoinaddress' => $binancecoinaddress, 'url' => $url]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 'Authorization: '.$apikey]);

        // Return response instead of printing.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Send request.
        $result = curl_exec($ch);
        curl_close($ch);

        // Decode the received JSON string
        $resultdecoded = json_decode($result, true);
    }

}
