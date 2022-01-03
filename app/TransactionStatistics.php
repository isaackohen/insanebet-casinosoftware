<?php

namespace App;

use App\Utils\APIResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jenssegers\Mongodb\Eloquent\Model;

class TransactionStatistics extends Model
{
    protected $collection = 'transaction_statistics';

    protected $connection = 'mongodb';

    protected $fillable = [
        'user', 'promocode', 'weeklybonus', 'partnerbonus', 'challenges', 'freespins_amount', 'faucet', 'depositbonus', 'deposit_total', 'deposit_count', 'withdraw_count', 'withdraw_total', 'challenges', 'rakeback', 'tip_sent', 'tip_sent_today', 'tip_received'
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public static function statsGet($userid)
    {
        $stats = self::where('user', $userid)->first();

        if (! $stats) {
            self::create([
                'user' => $userid,
                'promocode' => 0,
                'weeklybonus' => 0,
                'freespins_amount' => 0,
                'partnerbonus' => 0,
                'faucet' => 0,
                'challenges' => 0,
                'depositbonus' => 0,
                'deposit_total' => 0,
                'deposit_count' => 0,
                'withdraw_count' => 0,
                'withdraw_total' => 0,
                'vip_progress' => 0,
                'tip_received' => 0,
                'tip_sent' => 0,
                'tip_sent_today' => 0
            ]);
            $stats = self::where('user', $userid)->get()->toArray();
        }
        $stats = self::where('user', $userid)->get()->toArray();

        return $stats;
    }

    public static function statsUpdate($userid, $type, $amount)
    {
        $stats = self::where('user', $userid)->first();

        if (! $stats) {
            self::create([
                'user' => $userid,
                'promocode' => 0,
                'weeklybonus' => 0,
                'freespins_amount' => 0,
                'partnerbonus' => 0,
                'faucet' => 0,
                'challenges' => 0,
                'depositbonus' => 0,
                'deposit_total' => 0,
                'deposit_count' => 0,
                'withdraw_count' => 0,
                'withdraw_total' => 0,
                'vip_progress' => 0,
                'tip_received' => 0,
                'tip_sent' => 0,
                'tip_sent_today' => 0
             ]);
            $stats = self::where('user', $userid)->first();
        }

        $selectCurrent = $stats->$type;
        $stats->update([
            $type => round($selectCurrent ?? 0, 3) + round($amount, 3),
        ]);

        if ($type === 'deposit_total') {
            $stats->update([
                'deposit_count' => $stats->deposit_count + 1,
            ]);
        }
    }
}
