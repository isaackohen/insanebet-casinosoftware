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

class Challenges extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'challenges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game', 'game_name', 'minbet', 'game_image', 'game_provider', 'game_category', 'multiplier', 'winners', 'currency', 'sum', 'maxwinners', 'expires', 'expired', 'vip', 'completed',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'winners' => 'json',
        'expires' => 'datetime',
    ];

    public static function generate()
    {
        return strtoupper(substr(str_shuffle(md5(microtime())), 0, 8));
    }

    public static function complete($gameid)
    {
        $selectChallenge = self::where('game', '=', $gameid)->first();
        $selectChallenge->update([
            'completed' => 1,
        ]);
    }

    public static function check($gameid, $wager, $multi, $user)
    {
        $selectChallenge = self::where('game', '=', $gameid)->where('completed', '!=', 1)->first();

        //Log::warning('challenges info: '.$gameid.$wager.$multi.$user);
        if ($selectChallenge) {
            $selectGames = Gameslist::where('id', '=', $gameid)->first();

            if ($wager < $selectChallenge->minbet) {
                return;
            }
            if ($multi < $selectChallenge->multiplier) {
                return;
            }
            if ($selectChallenge->maxwinners != -1 && $selectChallenge->expired >= $selectChallenge->maxwinners) {
                self::complete($gameid);
            }
            if ($selectChallenge->expires->timestamp != Carbon::minValue()->timestamp && $selectChallenge->expires->isPast()) {
                self::complete($gameid);
            }

            $user = User::where('_id', $user)->first();

            if (in_array($user->_id, $selectChallenge->winners)) {
                return;
            }

            $winners = $selectChallenge->winners;
            array_push($winners, $user->_id);

            $selectChallenge->update([
                'expired' => $selectChallenge->expired + 1,
                'winners' => $winners,
            ]);

            $currency = Currency::find($selectChallenge->currency);
            $amount = $currency->convertUSDToToken($selectChallenge->sum);

            $stats = Statistics::where('user', $user->_id)->first();
            $notificationMessage = 'You have won challenge! We have added '.$selectChallenge->sum.'$ to your '.$currency->name().' balance.';
            self::complete($gameid);

            if ($stats) {
                $viplevel = $stats->viplevel;
                if ($viplevel > 0) {
                    $getcurrentViplevels = VIPLevels::where('level', '=', $viplevel)->first();
                    $bonus = round(($selectChallenge->sum / 100) * $getcurrentViplevels->challenges_bonus, 3);
                    $amount = $currency->convertUSDToToken(($selectChallenge->sum + $bonus));
                    $notificationMessage = 'You have won challenge! We have added '.$selectChallenge->sum.'$ + '.$bonus.'$ VIP bonus to your '.$currency->name().' balance.';
                }
            }

            Notification::send($user, new CustomNotification('Challenge Credited', $notificationMessage));
            $alertmessage = 'Challenge won by '.$user->name.' with a '.$multi.'x multiplier on '.$selectGames->name.'.';
            event(new PublicUserNotification('Challenge Winner', $alertmessage));

            $telegramChannel = Settings::get('telegram_public_channel');
            $imageGame = 'https://games.cdn4.dk/games'.$selectGames->image.'';
            $url = 'http://alerts.sh/api/alert/telegramImage?image='.$imageGame.'&message='.$alertmessage.'&button_text=Check available Challenges&button_url='.config('app.url').'/challenges/&channel='.$telegramChannel;
            $result = file_get_contents($url);

            $user->balance(Currency::find($selectChallenge->currency))->add($amount, Transaction::builder()->message('Challenge Winner')->get());
            TransactionStatistics::statsUpdate($user->_id, 'challenges', $amount);
        }
    }
}
