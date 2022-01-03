<?php

namespace App;

use App\Currency\Currency;
use App\Events\BalanceModification;
use App\Notifications\DatabaseNotification;
use App\Settings;
use App\Statistics;
use App\Token\NewAccessToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\BSON\Decimal128;
use NotificationChannels\WebPush\HasPushSubscriptions;
use RobThree\Auth\TwoFactorAuth;

class User extends \Jenssegers\Mongodb\Auth\User
{
    use Notifiable, HasPushSubscriptions, HasApiTokens;

    protected $connection = 'mongodb';

    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'access', 'client_seed', 'vip_discord_notified',
        'bonus_claim', 'ignore', 'private_profile', 'private_bets', 'name_history', 'latest_activity',
        'telegram_bonus', 'notification_bonus', 'ban', 'mute', 'weekly_bonus', 'weekly_bonus_obtained',
        'tfa', 'tfa_sec', 'tfa_enabled', 'tfa_persistent_key', 'tfa_onetime_key', 'email_notified', 'dismissed_global_notifications',
        'register_ip', 'login_ip', 'register_multiaccount_hash', 'login_multiaccount_hash',
        'referral', 'referral_wager_obtained', 'referral_bonus_obtained', 'referral_claimable', 'promocode_limit_reset', 'promocode_limit',
        'bot', 'favoriteGames', 'freespins',

        'vk', 'fb', 'google', 'telegram', 'steam',

        'btc', 'ltc', 'eth', 'bnb', 'inr', 'doge', 'bch', 'trx', 'algo', 'btg', 'celo', 'dash', 'eos', 'xrp', 'xlm', 'xtz', 'wbtc', 'zec', 'rub', 'busd', 'cake',
        'demo_btc', 'demo_ltc', 'demo_eth', 'demo_bnb', 'demo_doge', 'demo_bch', 'demo_trx', 'demo_algo', 'demo_btg', 'demo_celo', 'demo_dash', 'rxcg', 'demo_rxcg',
        'demo_eos', 'demo_xrp', 'demo_xlm', 'demo_xtz', 'demo_wbtc', 'demo_zec', 'demo_rub', 'bonus', 'pirate', 'wallet_cg_pirate', 'game1', 'bonusbattle_balance',

        'wallet_native_btc', 'wallet_native_ltc', 'wallet_native_eth', 'wallet_native_doge', 'wallet_native_bch', 'wallet_native_trx', 'wallet_np_rxcg',
        'wallet_bg_btc', 'wallet_bg_bch', 'wallet_bg_trx', 'wallet_bg_eos', 'wallet_bg_eth', 'wallet_bg_ltc', 'wallet_np_bnb', 'wallet_np_busd', 'wallet_cake',
        'wallet_bg_algo', 'wallet_bg_btg', 'wallet_bg_celo', 'wallet_bg_dash', 'wallet_bg_eos', 'wallet_bg_xrp', 'wallet_bg_xlm',
        'wallet_bg_xtz', 'wallet_bg_wbtc', 'wallet_bg_zec', 'wallet_np_btc', 'wallet_np_eth', 'wallet_np_ltc', 'wallet_np_doge', 'wallet_np_bch', 'wallet_np_trx', 'wallet_np_game1', 
        'wallet_trx_private_key', 'first_deposit_bonus', 'wallet_bonus', 'bonus_goal',  'bonus_wagered',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    public $hidden = [
        'password', 'remember_token', 'email', 'ignore', 'ban', 'notification_bonus', 'latest_activity',
        'tfa', 'tfa_sec', 'tfa_enabled', 'tfa_persistent_key', 'tfa_onetime_key', 'email_notified', 'dismissed_global_notifications',
        'register_ip', 'login_ip', 'register_multiaccount_hash', 'login_multiaccount_hash', 'vip_discord_notified',
        'referral', 'referral_wager_obtained', 'referral_bonus_obtained', 'referral_claimable', 'promocode_limit_reset', 'promocode_limit',
        'bot', 'freespins',

        'vk', 'fb', 'google', 'steam',

        'btc', 'ltc', 'eth', 'doge', 'bch', 'trx', 'algo', 'btg', 'celo', 'dash', 'eos', 'xrp', 'xlm', 'xtz', 'wbtc', 'zec', 'busd', 'cake', 'rxcg', 'demo_rxcg',
        'busd', 'bnb', 'inr', 'pirate',
        'demo_btc', 'demo_ltc', 'demo_eth', 'demo_doge', 'demo_bch', 'demo_trx', 'demo_algo', 'demo_btg', 'demo_celo', 'demo_dash',
        'demo_eos', 'demo_xrp', 'demo_xlm', 'demo_xtz', 'demo_wbtc', 'demo_zec', 'bonus', 'game1', 'bonusbattle_balance',

        'wallet_native_btc', 'wallet_native_ltc', 'wallet_native_eth', 'wallet_native_doge', 'wallet_native_bch', 'wallet_native_trx', 'wallet_np_rxcg',
        'wallet_bg_btc', 'wallet_bg_bch', 'wallet_bg_trx', 'wallet_bg_eos', 'wallet_bg_eth', 'wallet_bg_ltc',  'wallet_np_busd', 'wallet_cake',
        'wallet_bg_algo', 'wallet_bg_btg', 'wallet_bg_celo', 'wallet_bg_dash', 'wallet_bg_eos', 'wallet_bg_xrp', 'wallet_bg_xlm', 'wallet_np_game1', 
        'wallet_bg_xtz', 'wallet_bg_wbtc', 'wallet_bg_zec', 'wallet_np_btc', 'wallet_np_eth', 'wallet_np_ltc', 'wallet_np_doge', 'wallet_np_bch', 'wallet_np_trx',
        'wallet_cg_busd', 'wallet_cg_bnb', 'wallet_cg_pirate', 'wallet_bonus', 'bonus_goal', 'bonus_wagered',
        'wallet_trx_private_key',
    ];

    public function openGoogle($crud = false)
    {
        return '<a class="btn btn-sm btn-link" target="_blank" href="http://google.com?q='.urlencode($this->name).'" data-toggle="tooltip" title="Just a demo custom button."><i class="fa fa-search"></i> User Stats</a>';
    }

    /**
     * Some of the attributes should be hidden even for account owners.
     * @var array
     */
    public $alwaysHidden = [
        'register_multiaccount_hash', 'login_multiaccount_hash', 'register_ip', 'login_ip', 'wallet_trx_private_key', 'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'bonus_claim' => 'datetime',
        'mute' => 'datetime',
        'latest_activity' => 'datetime',
        'promocode_limit_reset' => 'datetime',
        'tfa_persistent_key' => 'datetime',
        'tfa_onetime_key' => 'datetime',
        'ignore' => 'json',
        'name_history' => 'json',
        'referral_wager_obtained' => 'json',
        'favoriteGames' => 'json',
    ];

    public static function getIp()
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return request()->ip();
    }

    public function isDismissed(GlobalNotification $globalNotification)
    {
        return in_array($globalNotification->_id, $this->dismissed_global_notifications ?? []);
    }

    public function dismiss(GlobalNotification $globalNotification)
    {
        $array = $this->$globalNotification->dismissed_global_notifications ?? [];
        array_push($array, $globalNotification->_id);
        $this->update([
            'dismissed_global_notifications' => $array,
        ]);
    }

    public function notifications()
    {
        return $this->morphMany(DatabaseNotification::class, 'notifiable')->orderBy('created_at', 'desc');
    }

    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('read_at');
    }

    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('read_at');
    }
    
    public function balance(Currency $currency): UserBalance
    {
        return new UserBalance($this, $currency);
    }

    public function clientCurrency(): Currency
    {
        return Currency::find($_COOKIE['currency'] ?? Currency::all()[0]->id()) ?? Currency::all()[0];
    }

    public function depositWallet(Currency $currency)
    {
        $wallet = $this->makeVisible('wallet_'.$currency->id())->toArray()['wallet_'.$currency->id()] ?? null;
        if ($wallet == null) {
            $wallet = $currency->newWalletAddress();
            if ($wallet !== 'Error') {
                $this->update([
                    'wallet_'.$currency->id() => $wallet,
                ]);
            }
        }

        return $wallet;
    }

    public function total(Currency $currency)
    {
        $statistics = Statistics::where('user', $this->_id)->first();

        return $statistics->data['usd_wager'] ?? 0;
    }

    public function games()
    {
        $statistics = Statistics::where('user', $id)->first();
        $games = 0;
        foreach (Currency::all() as $currency) {
            $var1 = 'bets_'.$currency->id();
            $games += $statistics->data[$var1] ?? 0;
        }

        return $games;
    }

    public function getInvestmentProfit(Currency $currency, bool $sub, bool $stopAtZero = true)
    {
        $profit = 0;
        foreach (Investment::where('user', $this->_id)->where('status', 0)->where('currency', $currency->id())->get() as $investment) {
            $profit += $investment->getProfit() - ($sub ? $investment->amount : 0);
        }

        return $stopAtZero == false ? $profit : ($profit < 0 ? 0 : $profit);
    }

    public function rakeback(): float
    {
        $statistics = Statistics::where('user', $this->_id)->first();

        return floatval(number_format((($statistics->current_rakeback ?? 0) / 100), 2, '.', '') ?? 0);
    }

    public function vipLevel(): int
    {
        $select = Statistics::where('user', $this->_id)->first();

        return $select->viplevel ?? 0;
    }

    public function vipBonus(): float
    {
        return 0;
    }

    public function tfa(): TwoFactorAuth
    {
        return new TwoFactorAuth('casino.managment/'.$this->name);
    }

    public function validate2FA(bool $persist): bool
    {
        $token = $persist ? ($this->tfa_persistent_key ?? null) : ($this->tfa_onetime_key ?? null);

        return ($this->tfa_enabled ?? false) === false || ($token != null && ! $token->isPast());
    }

    public function reset2FAOneTimeToken()
    {
        $this->update(['tfa_onetime_key' => null]);
    }

    public function createToken(array $abilities = ['*'])
    {
        $token = $this->tokens()->create([
            'name' => $this->_id,
            'token' => hash('sha256', $plainTextToken = Str::random(80)),
            'abilities' => $abilities,
        ]);

        return new NewAccessToken($token, $token->id.'|'.$plainTextToken);
    }
}

class UserBalance
{
    private User $user;

    private Currency $currency;

    private bool $quiet = false;

    private bool $demo = false;

    private float $minValue = 0.00000000;

    public function __construct(User $user, Currency $currency)
    {
        $this->user = $user;
        $this->currency = $currency;
    }

    public function quiet()
    {
        $this->quiet = true;

        return $this;
    }

    public function demo($set = true)
    {
        $this->demo = $set;

        return $this;
    }

    public function get(): float
    {
        $value = floatval(($this->user->{$this->getColumn()} ?? new Decimal128($this->minValue))->jsonSerialize()['$numberDecimal']);

        return $value < 0 ? 0 : floatval(number_format($value, $this->currency->walletId() === 'rub' ? 2 : 8, '.', ''));
    }

    private function getColumn()
    {
        return $this->demo ? 'demo_'.$this->currency->walletId() : $this->currency->walletId();
    }

    public function add(float $amount, array $transaction = null, $txid = null)
    {
        $this->user->update([
            $this->getColumn() => new Decimal128(strval($this->get() + $amount)),
        ]);

        if ($this->quiet == false) {
            event(new BalanceModification($this->user, $this->currency, 'add', $this->demo, $amount, 0));
        }
        Transaction::create([
            'user' => $this->user->_id,
            'demo' => $this->demo,
            'currency' => $this->currency->id(),
            'new' => $this->get(),
            'old' => $this->get() - $amount,
            'amount' => $amount,
            'quiet' => $this->quiet,
            'data' => $transaction ?? [],
            'meta' => $transaction['meta'] ?? [],
        ]);
    }

    public function subtract(float $amount, array $transaction = null)
    {
        $value = $this->get() - $amount;
        if ($value < 0) {
            $value = 0;
        }
        $this->user->update([
            $this->getColumn() => new Decimal128(strval($value)),
        ]);

        if ($this->quiet == false) {
            event(new BalanceModification($this->user, $this->currency, 'subtract', $this->demo, $amount, 0));
        }
        Transaction::create([
            'user' => $this->user->_id,
            'demo' => $this->demo,
            'currency' => $this->currency->id(),
            'new' => $this->get(),
            'old' => $this->get() + $amount,
            'amount' => -$amount,
            'quiet' => $this->quiet,
            'data' => $transaction ?? [],
            'meta' => $transaction['meta'] ?? [],
        ]);
    }
}
