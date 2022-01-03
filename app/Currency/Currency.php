<?php

namespace App\Currency;

use App\Currency\BitGo\BitGoCurrency;
use App\Currency\Option\WalletOption;
use App\Events\Deposit;
use App\Invoice;
use App\Settings;
use App\Statistics;
use App\Currency\ChainGateway\RXC;
use App\Currency\ChainGateway\ChainGatewaySupport;

use App\Transaction;
use App\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MongoDB\BSON\Decimal128;

abstract class Currency
{
    abstract public function id(): string;

    abstract public function walletId(): string;

    abstract public function name(): string;

    abstract public function alias(): string;

    abstract public function conversionID(): string;

    abstract public function displayName(): string;

    abstract public function style(): string;

    abstract public function newWalletAddress(): string;

    abstract protected function options(): array;

    public function minBet(): float
    {
        return 0.0000001;
    }

    public function tokenPrice()
    {
        try {
            if (! Cache::has('conversions:'.$this->conversionID())) {
                Cache::put('conversions:'.$this->conversionID(), file_get_contents("https://api.coingecko.com/api/v3/coins/{$this->conversionID()}?localization=false&market_data=true"), now()->addHours(1));
            }
            $json = json_decode(Cache::get('conversions:'.$this->conversionID()));

            return $json->market_data->current_price->inr;
        } catch (\Exception $e) {
            try {
                $result = Cache::remember('rate', 60, function () {
                    return file_get_contents("https://min-api.cryptocompare.com/data/pricemulti?fsyms={$this->name()}&tsyms=INR");
                });
                $price = json_decode($result, true);

                return $price[$this->name()]['INR'];
            } catch (\Exception $e) {
                return 1;
            }
        }
    }

    public function convertUSDToToken(float $usdAmount)
    {
        return number_format(($usdAmount / $this->tokenPrice()), 7, '.', '');
    }

    public function convertTokenToUSD(float $tokenAmount)
    {
        return number_format(($tokenAmount * $this->tokenPrice()), 7, '.', '');
    }

    public function getBotBet()
    {
        $getBotbet = $this->randomBotBet($this->convertUSDToToken(0.15), $this->convertUSDToToken(5));

        return $getBotbet;
    }

    /**
     * Gets random bet value. Higher values are less common.
     * @param $min
     * @param $max
     * @return mixed
     */
    protected function randomBotBet(float $min, float $max)
    {
        try {
            $diff = 100000000;

            return min(mt_rand($min * $diff, $max * $diff) / $diff, mt_rand($min * $diff, $max * $diff) / $diff);
        } catch (\Exception $e) {
            return $this->randomBotBet(1, 100);
        }
    }

    /** @return WalletOption[] */
    public function getOptions(): array
    {
        return array_merge($this->options(), [
            new class extends WalletOption {
                public function id()
                {
                    return 'icon';
                }

                public function name(): string
                {
                    return 'Icon crypto';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'demo';
                }

                public function name(): string
                {
                    return 'Demo value';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'fee';
                }

                public function name(): string
                {
                    return 'Transaction fee';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'withdraw';
                }

                public function name(): string
                {
                    return 'Minimal withdraw amount';
                }
            },
            new class extends WalletOption {
                public function id()
                {
                    return 'withdraw_manual_trigger';
                }

                public function name(): string
                {
                    return 'Withdrawal manually if balance including total withdrawal amount is higher than';
                }
            },
        ]);
    }

    public function option(string $key, string $value = null): string
    {
        if ($value == null) {
            if (Cache::has('currency:'.$this->walletId().':'.$key)) {
                return json_decode(Cache::get('currency:'.$this->walletId().':'.$key), true)[$key] ?? '1';
            }

            return \App\Currency::where('currency', $this->walletId())->first()->data[$key] ?? '1';
        }

        $data = \App\Currency::where('currency', $this->walletId())->first();

        if (! $data) {
            $data = \App\Currency::create(['currency' => $this->walletId(), 'data' => []]);
        }

        $data = $data->data;
        $data[$key] = $value;

        \App\Currency::where('currency', $this->walletId())->first()->update([
            'data' => $data,
        ]);

        Cache::forget('currency:'.$this->walletId().':'.$key);
        Cache::put('currency:'.$this->walletId().':'.$key, json_encode($data), now()->addYear());

        return $value;
    }

    abstract public function isRunning(): bool;

    /**
     * @param string|null $wallet Null for every transaction except local nodes
     * @return mixed
     */
    abstract public function process(string $wallet = null);

    abstract public function send(string $from, string $to, float $sum);

    abstract public function setupWallet();

    abstract public function depositmethod();

    abstract public function withdrawmethod();

    abstract public function coldWalletBalance(): float;

    abstract public function hotWalletBalance(): float;

    public static function toCurrencyArray(array $array, $all = false)
    {
        $currency = [];
        $fdb = auth('sanctum')->guest() ? null : auth('sanctum')->user()->first_deposit_bonus ?? null;
        foreach ($array as $c) {
            if (auth('sanctum')->guest() ? null : auth('sanctum')->user()->access !== 'admin') {
                if ((! $fdb) && ($c->id() === 'local_bonus')) {
                    continue;
                }
            }
            $currency = array_merge($currency, [
                $c->id() => [
                    'id' => $c->id(),
                    'walletId' => $c->walletId(),
                    'name' => $c->name(),
                    'displayName' => $c->displayName(),
                    'icon' => $c->iconId(),
                    'style' => $c->style(),
                    'price' => $c->tokenPrice(),
                    'withdrawFee' => floatval($c->option('fee')),
                    'minimalWithdraw' => floatval($c->option('withdraw')),
                    'highRollerRequirement' => floatval(Settings::get('high_roller_requirement') / $c->tokenPrice()),
                    'min_bet' => floatval(Settings::get('min_bet') / $c->tokenPrice()),
                    'max_bet' => floatval(Settings::get('max_bet') / $c->tokenPrice()),
                    'balance' => [
                        'real' => auth('sanctum')->guest() ? null : auth('sanctum')->user()->balance($c)->get(),
                        'demo' => auth('sanctum')->guest() ? null : auth('sanctum')->user()->balance($c)->demo(true)->get(),
                    ],
                ],
                'vipClosest' => self::find(Settings::get('bonus_currency'))->name(),
                'vipClosestId' => self::find(Settings::get('bonus_currency'))->id(),
                'vipClosestWager' => auth('sanctum')->guest() ? 0 : (Statistics::where('user', auth('sanctum')->user()->_id)->first()->data['usd_wager'] ?? 0),
                'depoBonus' => Settings::get('first_deposit_status'),
                'depoBonusRoll' => floatval(Settings::get('first_deposit_rules')),
				'bonusBattle' => Settings::get('bonus_battles_status')
            ]);
        }

        return $currency;
    }

    public static function getAllSupportedCoins(): array
    {
        return [
            //new Local\Rub(),
            new Local\Usd(),
            new Local\Inr(),
            new Local\Bonus(),
            //new Native\Bitcoin(),
            //new Native\Ethereum(),
            //new Native\Litecoin(),
            //new Native\Dogecoin(),
            //new Native\Litecoin(),
            //new Native\BitcoinCash(),
            //new NowPayments\BNB(),
            new NowPayments\Bitcoin(),
            //new NowPayments\Ethereum(),
            //new NowPayments\Cake(),
            //new NowPayments\BUSD(),
            //new NowPayments\Litecoin(),
            //new NowPayments\Dogecoin(),
            //new NowPayments\Litecoin(),
            //new NowPayments\BitcoinCash(),
            //new NowPayments\RXCG(),
            //new NowPayments\GAME1(),
            //new NowPayments\RXCG(),
            //new NowPayments\Tron(),
            //new СhainGateway\BNB(),
            //new СhainGateway\BUSD(),
            //new СhainGateway\Pirate(),
            //new BitGo\Bitcoin(),
            //new BitGo\BitcoinCash(),
            //new BitGo\BitcoinGold(),
            //new BitGo\WrappedBitcoin(),
            //new BitGo\Algorand(),
            //new BitGo\Celo(),
            //new BitGo\Dash(),
            //new BitGo\EOS(),
            //new BitGo\Ethereum(),
            //new BitGo\Litecoin(),
            //new BitGo\Ripple(),
            //new BitGo\Stellar(),
            //new BitGo\Tezos(),
            //new BitGo\Tron(),
            //new BitGo\ZCash()
        ];
    }

    public static function all(): array
    {
        $currencies = json_decode(Settings::get('currencies', '["native_btc"]', true));
        $result = [];
        foreach ($currencies as $currency) {
            array_push($result, self::find($currency));
        }

        return $result;
    }

    public static function getByWalletId($walletId): array
    {
        $result = [];
        foreach (self::getAllSupportedCoins() as $coin) {
            if ($coin->walletId() === $walletId) {
                array_push($result, $coin);
            }
        }

        return $result;
    }

    public static function find(string $id): ?self
    {
        foreach (self::getAllSupportedCoins() as $currency) {
            if ($currency->id() == $id) {
                if (\App\Currency::where('currency', $currency->id())->first() == null) {
                    \App\Currency::create([
                        'currency' => $currency->id(),
                        'data' => [],
                    ]);
                }

                return $currency;
            }
        }

        return null;
    }

    protected function accept(int $confirmations, string $wallet, string $id, float $sum)
    {
        $user = User::where('wallet_'.$this->id(), $wallet)->first();
        if ($user == null) {
            return false;
        }

        $invoice = Invoice::where('id', $id)->first();
        if ($invoice == null) {
            $invoice = Invoice::create([
                'user' => $user->_id,
                'sum' => new Decimal128($sum),
                'type' => 'currency',
                'currency' => $this->id(),
                'id' => $id,
                'confirmations' => $confirmations,
                'status' => 0,
            ]);
            event(new Deposit($user, $this, $sum));
        } else {
            $invoice->update([
                'confirmations' => $confirmations,
            ]);
        }

        if ($invoice->status == 0 && $invoice->confirmations >= intval($this->option('confirmations'))) {
            $invoice->update(['status' => 1]);
            $user->balance($this)->add($sum, Transaction::builder()->message('Deposit')->get());

            if (! ($this instanceof BitGoCurrency)) {
                $this->send($wallet, $this->option('transfer_address'), $sum);
            }

            if ($user->referral) {
                $referrer = User::where('_id', $user->referral)->first();

                $commissionPercent = 0;

                switch ($referrer->vipLevel()) {
                    case 0: $commissionPercent = 1; break;
                    case 1: $commissionPercent = 2; break;
                    case 2: $commissionPercent = 3; break;
                    case 3: $commissionPercent = 4; break;
                    case 4: $commissionPercent = 5; break;
                    case 5: $commissionPercent = 7; break;
                }

                if ($commissionPercent !== 0) {
                    $commission = ($commissionPercent * $sum) / 100;
                    $referrer->balance($this)->add($commission, Transaction::builder()->message('Affiliate commission ('.$commissionPercent.'% from '.$sum.' .'.$this->name().')')->get());
                }
            }
        }

        return true;
    }
}
