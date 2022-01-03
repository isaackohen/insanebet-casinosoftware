<?php

namespace App;

use App\Currency\Currency;
use App\Events\PublicUserNotification;
use App\Events\UserNotification;
use App\Notifications\CustomNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Jenssegers\Mongodb\Eloquent\Model;
use App\VIPLevels;
use Illuminate\Support\Facades\Log;

class Statistics extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'user_statistics';

    protected $fillable = [
        'user',
        'data',
        'vip_progress',
        'viplevel',
        'current_rakeback',
        'affiliate_rakeback',
        'affiliate_total',
        'last_rakeback',
    ];

    protected $casts = [
        'data' => 'json',
        'bonusbattle_balance' => 'json',
    ];

    public static function insert($user, $currency, $wager, $multiplier, $profit, $type = null)
    {
        $stats = self::where('user', $user)->first();
        if (! $stats) {
            $stats = self::create([
                'user' => $user,
                'data' => [],
                'viplevel' => 0,
                'vip_progress' => 0,
                'current_rakeback' => 0,
                'last_rakeback' => 0,
                'affiliate_total' => 0,
                'affiliate_rakeback' => 0,
                'bonusbattle_balance' => [],
            ]);
        }


        $multiAllow = 0;
        if ($multiplier > floatval(1.25) || $multiplier < floatval(0.85)) {
            $multiAllow = '1';
        }
        $tokenPrice = Currency::find($currency)->tokenPrice() ?? 0;
        $wagerAmount = round($wager * $tokenPrice, 5);

        if ($wagerAmount > 0.08 && $multiAllow === '1') {

            $vipModifier = 1;
            $currentViplevel = ($stats->viplevel ?? 0);
            $userGet = User::where('_id', $user)->first();

            $amountMod = $wagerAmount;
            if($type === 'external') {
                $amountMod = $wagerAmount * 0.9;
            }
            if($currency === 'local_bonus') {
                $amountMod = floatval($wagerAmount * 0.85);
                if($type === 'external') {
                $amountMod = floatval($wagerAmount * 0.5);
            }
            if ($userGet->first_deposit_bonus === 'used') {
                $currentWagered = $userGet->bonus_wagered ?? 0;
                    User::where('_id', $user)->update([
                        'bonus_wagered' => number_format(($currentWagered ?? 0) + $amountMod, 6, '.', ''),
                    ]);
            }

            }

            //Add VIP progression
            $stats->update([
                'vip_progress' => round(($stats->vip_progress ?? 0), 4) + round(($amountMod * $vipModifier), 4),
            ]);
            $getcurrentViplevels = VIPLevels::where('level', '=', ''.$currentViplevel.'')->first();

            if($currentViplevel > 0) {
                $rakeBonus = floatval(($amountMod / 100) * $getcurrentViplevels->rake_percent);
                $stats->update([
                    'current_rakeback' => ($stats->current_rakeback ?? 0) + $rakeBonus,
                ]);

                $referral = $userGet->referral ?? null;

                if($referral != null) {
                    $stats->update([
                        'affiliate_rakeback' => number_format(($stats->affiliate_rakeback ?? 0) + ($rakeBonus / 2), 6, '.', ''),
                    ]);
                }
            }

            $getNextLevel = ($stats->viplevel ?? 0) + 1;
            $getViplevels = VIPLevels::where('level', '=', ''.$getNextLevel.'')->first();
            //Check if next VIP wager requirement is reached, then change VIP level
            if (($stats->vip_progress ?? 0) > $getViplevels->start) {
                $getFs = $getViplevels->fs_bonus;
                if ($stats->viplevel === 0) {
                    event(new PublicUserNotification('New VIP player', ' '.$userGet->name.' has just joined the Player VIP Club. Welcome him in the chat!'));
                }
                $stats->update([
                    'vip_progress' => 0,
                    'viplevel' => ($stats->viplevel ?? 0) + 1,
                ]);
                $eventUpdated = event(new \App\Events\UserNotification($userGet, 'VIP Club!', 'You have reached new VIP level.'));
                $notificationMessage = 'You have reached VIP Level '.$stats->viplevel.'. We have added '.$getFs.' free spins to your account. Make sure to check VIP page for more info.';
                Notification::send($userGet, new CustomNotification('New VIP Level Reached', $notificationMessage));

                //Add free spins if level up
                User::where('_id', $userGet->_id)->update([
                    'freespins' => ($userGet->freespins ?? 0) + ($getFs),
                ]);
            }
        }

        $var_bets = 'bets_'.$currency;
        $var_wins = 'wins_'.$currency;
        $var_loss = 'loss_'.$currency;
        $var_wagered = 'wagered_'.$currency;
        $var_profit = 'profit_'.$currency;

        $data = $stats->data ?? null;
        if ($data == null) {
            $keys = ['usd_wager'];
            $data = array_fill_keys($keys, '0');
        }

        if (! array_key_exists($var_bets, $data)) {
            $keys = ['games_played', 'usd_wins', $var_bets, $var_wins, $var_loss, $var_wagered, $var_profit];
            $newData = array_fill_keys($keys, '0');
            $data = array_merge($data, $newData);
        }
        $data['usd_wager'] += $wagerAmount;
        $data['usd_wins'] += $profit * Currency::find($currency)->tokenPrice() ?? 0;
        $data['games_played'] += 1;
        $data[$var_bets] += 1;
        $data[$var_wins] += $profit > 0 ? ($multiplier < 1 ? 0 : 1) : 0;
        $data[$var_loss] += $profit > 0 ? ($multiplier < 1 ? 1 : 0) : 1;
        $data[$var_wagered] += $wager;
        $data[$var_profit] += $profit > 0 ? ($multiplier < 1 ? -($wager) : ($profit)) : -($wager);

        $stats->update([
            'data' => $data,
        ]);
    }
}
