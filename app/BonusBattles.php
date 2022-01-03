<?php

namespace App;

use App\Currency\Currency;
use App\Events\PublicUserNotification;
use App\Gameslist;
use App\Notifications\CustomNotification;
use App\Settings;
use App\Statistics;
use App\User;
use App\VIPLevels;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Jenssegers\Mongodb\Eloquent\Model;

class BonusBattles extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'bonusbattles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'players_max', 'players_active', 'players', 'players_counter', 'player_id_1', 'player_balance_1', 'player_final_1', 'player_id_2', 'player_balance_2', 'player_final_2', 'player_id_3', 'player_balance_3', 'player_final_3', 'player_id_4', 'player_balance_4', 'player_final_4', 'player_name_1', 'player_name_2', 'player_name_3', 'player_name_4', 'players_balance',  'winner_id', 'winner_amount', 'winner_name', 'currency', 'stake', 'room_id', 'room_state', 'created_at', 'game', 'g_name', 'g_img', 'g_prov', 'g_cat', 'total_bet', 'total_win', 'tx_ids', 'expires', 'expires_started', 'completed', 'claimed'
    ];



    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'players_balance', 'player_balance_1', 'player_balance_3', 'player_balance_4', 'player_balance_2', 'winner_id', 'winner_amount', 'winner_name',


    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'players' => 'json',
        'players_balance' => 'json',
        'tx_ids' => 'json',
        'expires' => 'datetime',
    ]; 

    public static function generate()
    {
        return strtoupper(substr(str_shuffle(md5(microtime())), 0, 8));
    }


}
