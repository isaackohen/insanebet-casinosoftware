<?php

namespace App;

use App\Utils\APIResponse;
use App\Utils\Exception\UnsupportedOperationException;
use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;

class Leaderboard extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'leaderboard';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'currency', 'wager', 'profit', 'time', 'user', 'usd_wager', 'usd_profit',
    ];

    public static function insert(Game $game)
    {
        if ($game->status === 'in-progress' || $game->status === 'cancelled' || $game->demo) {
            return;
        }

        self::insertGame('today', $game);
        self::insertGame('all', $game);
    }

    private static function insertGame($type, Game $game)
    {
        $currency = \App\Currency\Currency::find($game->currency);

        $entry = self::where('type', $type)->where('currency', $currency->walletId())->where('user', $game->user)->where('time', self::toTime($type))->first();
        $entryusd = self::where('type', $type)->where('currency', 'usd')->where('user', $game->user)->where('time', self::toTime($type))->first();
        $usd_wager = $game->wager * $currency->tokenPrice();
        $usd_profit = $game->profit * $currency->tokenPrice();

        if (! $entry) {
            self::create([
                'type' => $type,
                'currency' => $currency->walletId(),
                'wager' => $game->wager,
                'profit' => $game->profit,
                'time' => self::toTime($type),
                'user' => $game->user,
            ]);
            if (! $entryusd) {
                self::create([
                    'type' => $type,
                    'currency' => 'usd',
                    'usd_wager' => $usd_wager,
                    'usd_profit' => $usd_profit,
                    'time' => self::toTime($type),
                    'user' => $game->user,
                ]);

                return;
            }
            $entryusd->update([
                'usd_wager' => $entryusd->usd_wager + $usd_wager,
                'usd_profit' => $entryusd->usd_profit + $usd_profit,
            ]);

            return;
        }

        $entryusd->update([
            'usd_wager' => $entryusd->usd_wager + $usd_wager,
            'usd_profit' => $entryusd->usd_profit + $usd_profit,
        ]);

        $entry->update([
            'wager' => $entry->wager + $game->wager,
            'profit' => $entry->profit + $game->profit,
        ]);
    }

    /**
     * @param $positions
     * @param string $type today|all
     * @param string $currency
     * @param string $orderBy wager|profit
     * @return array
     */
    public static function getLeaderboard($positions, string $type, Currency\Currency $currency, string $orderBy = 'wager'): array
    {
        $result = [];
        foreach (self::where('type', $type)->where('currency', $currency->walletId())->where('time', self::toTime($type))->orderByDesc($orderBy)->take($positions)->get() as $entry) {
            array_push($result, [
                'entry' => $entry->toArray(),
                'user' => User::where('_id', $entry->user)->first()->toArray(),
            ]);
        }

        return $result;
    }

    public static function getLeaderboardByUsd($positions, string $type, string $orderBy = 'usd_profit'): array
    {
        $result = [];
        if (self::where('type', $type)->first() == null) {
            return [];
        }
        foreach (self::where('type', $type)->where('currency', 'usd')->where('time', self::toTime($type))->orderByDesc($orderBy)->take($positions)->get() as $entry) {
            array_push($result, [
                'entry' => $entry->toArray(),
                'user' => User::where('_id', $entry->user)->first()->toArray(),
            ]);
        }

        return $result;
    }

    private static function toTime($type)
    {
        switch ($type) {
            case 'today': $mark = Carbon::today()->timestamp; break;
            case 'all': $mark = Carbon::minValue()->timestamp; break;
            default: throw new UnsupportedOperationException('Invalid leaderboard type: '.$type);
        }

        return $mark;
    }
}
