<?php

namespace App\Currency\СhainGateway;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use App\Invoice;
use App\Currency\ChainGateway\RXC;
use App\Settings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

abstract class СhainGatewaySupport extends Currency
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
        return true;
    }

    public function nowpayments()
    {
        return null;
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
            if ($this->option('contract_address') === '0') {
                $this->subcribeToBnb($apikey, $resultdecoded['binancecoinaddress'], $ipn);
            } else {
                $this->subcribeToToken($apikey, $this->option('contract_address'), $resultdecoded['binancecoinaddress'], $ipn);
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
    }

    public function setupWallet(): ?string
    {
        return null;
    }

    protected function options(): array
    {
        return [
            new class extends WalletOption {
                public function id()
                {
                    return 'withdraw_address';
                }

                public function name(): string
                {
                    return 'Hot Wallet (withdrawal) address';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'fund_address';
                }

                public function name(): string
                {
                    return 'Fund address';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'contract_address';
                }

                public function name(): string
                {
                    return 'Contract Address';
                }
            },
        ];
    }

    public function depositmethod(): string
    {
        return 'chaingateway';
    }

    public function withdrawmethod(): string
    {
        return 'chaingateway';
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
