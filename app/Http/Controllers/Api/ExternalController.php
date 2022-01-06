<?php

namespace App\Http\Controllers\Api;

use App\Challenges;
use App\Currency\Currency;
use App\Events\LiveFeedGame;
use App\Game;
use App\BonusBattles;
use App\Gameslist;
use App\Leaderboard;
use App\Settings;
use App\Statistics;
use App\Transaction;
use App\User;
use App\Utils\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; 
use Illuminate\Support\Facades\Http;
use App\Events\BonusBattleUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\UserNotification;
use App\Events\BonusBattleWrongBet;


class ExternalController
{

    public function warElementsCallback(Request $request)
    {
        $data = $request->all();
        if($data['status'] == 'Opened'){
            $game = \App\Games\Kernel\Game::find('warofelements');
            $game->NextGameLive();
            $game->state()->pushData(['url' => $data['videoUrl']['hls'], 'event' => $data['eventId']]);
        }
        if($data['status'] == 'Closed'){
            $game = \App\Games\Kernel\Game::find('warofelements');
            $game->DisableBetAcceptingGameLive();
            $game->state()->pushData(['event' => $data['eventId']]);
        }
        if($data['status'] == 'Drawing'){
            $game = \App\Games\Kernel\Game::find('warofelements');
            $game->state()->pushData(['event' => $data['eventId']]);
        }
        if($data['status'] == 'Completed'){
            $game = \App\Games\Kernel\Game::find('warofelements');
            $game->state()->pushData(
                [
                    'result' => [
                        'dealer' => $data['result']['dealer'],
                        'player' => $data['result']['player']
                    ]
                ]
            );
            $game->FinishGameLive();
        }
        return [];
    }
    

    public function methodBalance(Request $request)
    {
        Log::alert($request->fullUrl());
        $userId = $request['playerid'];
        $user = User::where('_id', $userId)->first();

        if (is_numeric($request['currency']) === true) {
            $roomExists = BonusBattles::where('room_id', '=', (int) $request['currency'])->first();
            if (in_array($request['playerid'], $roomExists->players)) {
            $playerNumber = array_search($request['playerid'], $roomExists->players);
            $data = $roomExists->players;
            $playerBalanceVar = 'player_balance_'.$playerNumber + 1;
            $getBalanceUSD = intval($roomExists->$playerBalanceVar * 100);
            }

        } else {
            $currency = Currency::find($request['currency']);
            $getBalance = $user->balance($currency)->get();
            $getBalanceUSD = intval($currency->convertTokenToUSD($getBalance) * 100);
        }

        $responsePayload = ['status' => 'ok', 'result' => ['balance' => $getBalanceUSD, 'freegames' => 0]];

        return json_encode($responsePayload); 
    }

    public function methodBet(Request $request)
    {
        
        Log::alert($request->fullUrl());
        $user = User::where('_id', $request['playerid'])->first();
        $currency = Currency::find($request['currency']);
        $win = $request['win'];
        $bet = $request['bet'];
        $final = $request['final'];
        $gameid = $request['gameid'];
        $roundid = $request['roundid'];
        $apikey = config('settings.api_key');
        $apisecret = config('settings.api_secret');
        $sign = $request['sign'];


        if (is_numeric($request['currency']) === true) {
            $roomExists = BonusBattles::where('room_id', '=', (int) $request['currency'])->where('game', '=', $gameid)->first();
            if (in_array($user->_id, $roomExists->players)) {

            $playerNumber = array_search($user->_id, $roomExists->players) + 1;
            $playerBalanceVar = 'player_balance_'.$playerNumber;
            $playerNameVar = 'player_name_'.$playerNumber;
            $playerFinalVar = 'player_final_'.$playerNumber;
            $playerFinalState = $roomExists->$playerFinalVar;

            $getBalanceUSD = intval($roomExists->$playerBalanceVar * 100);
            $getBetInt = intval($request['bet']);

                //Check if first bet is same as the bonus stake
                if($getBetInt !== $getBalanceUSD && $getBetInt > 0) {
                    $responsePayload = ['status' => 'error', 'result' => ['freegames' => 0]];
                    event(new BonusBattleWrongBet($user, $roomExists->room_id, $roomExists->game));
                    event(new UserNotification($user, 'Bonus Battle '.$roomExists->room_id, 'You did not pick right bet, make sure to only buy the Buy Feature or Buy Free Spins in the slot for the same amount as the Battle Stake amount.'));
                    return json_encode($responsePayload);
                    die;
                }

                //Check if first bet is same as the bonus stake
            if($roomExists->$playerFinalVar === "finished") {
                    $responsePayload = ['status' => 'error', 'result' => [['freegames' => 0]]];
                    event(new BonusBattleWrongBet($user, $roomExists->room_id, $roomExists->game));
                    event(new UserNotification($user, 'Bonus Battle '.$roomExists->room_id, 'You finished playing already, return to the battle lobby.'));
					event(new BonusBattleUpdate($roomExists));
                    return json_encode($responsePayload);
                    die;
            }

            if($roomExists->$playerFinalVar === "waiting") {


                //Change status to started
                $roomExists->update(['player_final_'.$playerNumber => 'started']);
				event(new BonusBattleUpdate($roomExists));

                $responsePayload = ['status' => 'ok', 'result' => ['balance' => 0, 'freegames' => 0]];
                return json_encode($responsePayload);

            }

            if($roomExists->$playerFinalVar === "started" & $final === "0") {

                //Add up balances 
                $IntBalance = intval(($getBalanceUSD) + $request['win']);


                $responsePayload = ['status' => 'ok', 'result' => ['balance' => 0, 'freegames' => 0]];
                return json_encode($responsePayload);
            }

            if($roomExists->$playerFinalVar === "started" && $final === "1") {

                $FloatBalance = floatval($request['totalWin'] / 100);

                //Finish & close
                $roomExists->update(['player_final_'.$playerNumber => 'finished']);
                $roomExists->update(['player_balance_'.$playerNumber => $FloatBalance]);
                $roomExists->update(['players_counter' => $roomExists->players_counter - 1]);

                if($roomExists->players_counter === 0) {
                $roomExists->update(['room_state' => 'completed']);

                $win1 = $roomExists->player_balance_1 ?? 0;
                $win2 = $roomExists->player_balance_2 ?? 0;
                $win3 = $roomExists->player_balance_3 ?? 0;
                $win4 = $roomExists->player_balance_4 ?? 0;
                $arr = array('1' => $win1, '2' => $win2, '3' => $win3, '4' => $win4);
                $maxVal = max($arr);
                $maxKey = array_search($maxVal, $arr);
                $summedUp = $win1 + $win2 + $win3 + $win4;
                $winnerUserIdVar = 'player_id_'.$maxKey;
                $winnerNameVar = 'player_name_'.$maxKey;
                $winnerName = $roomExists->$winnerNameVar;
                $winnerUserId = $roomExists->$winnerUserIdVar;

                $roomExists->update(['winner_id' => $winnerUserId]);
                $roomExists->update(['winner_amount' => $summedUp]);
                $roomExists->update(['winner_name' => $winnerName]);

                    foreach($roomExists->players as $player) {
                        if($player !== $winnerUserId) {
                            $userSearch = User::where('_id', $player)->first();
                            event(new UserNotification($userSearch, 'Bonus Battle Update - ID'.$roomExists->room_id, 'You have lost bonus battle. Battle has been won by '.$winnerName.' with for total of '.$summedUp.'$.'));
                        } else {
                            $userSearch = User::where('_id', $player)->first();
                            event(new UserNotification($userSearch, 'Bonus Battle Update - ID'.$roomExists->room_id, 'You have won the bonus battle for total of '.$summedUp.'$. Go to the Bonus Battle Lobby to claim your winnings.'));
                        }
                    }
					event(new BonusBattleUpdate($roomExists->makeVisible(['winner_name'])->makeVisible(['winner_id'])->makeVisible(['winner_amount'])->makeVisible(['player_balance_1'])->makeVisible(['player_balance_2'])->makeVisible(['player_balance_3'])->makeVisible(['player_balance_4'])));
                } else {
                    foreach($roomExists->players as $player) {
                        $userSearch = User::where('_id', $player)->first();
                        event(new UserNotification($userSearch, 'Bonus Battle Update - ID'.$roomExists->room_id, 'Player '.$roomExists->$playerNameVar.' has finished bonus game, waiting for others.'));
                    }
					event(new BonusBattleUpdate($roomExists));
                }

                $responsePayload = ['status' => 'ok', 'result' => ['balance' => $request['totalWin'], 'freegames' => 0]];
                return json_encode($responsePayload);
            }
        }            
            return [];
        }

        $roundIdExists = Game::where('server_seed', $roundid)->where('gameid', $gameid)->where('user', $request['playerid'])->first();
        if ($final === 1 && $roundIdExists) {
            $getBalance = $user->balance($currency)->get();
            $getBalanceUSD = intval($currency->convertTokenToUSD($getBalance) * 100);
            $responsePayload = ['status' => 'ok', 'result' => ['balance' => $getBalanceUSD, 'freegames' => 0]];

        return json_encode($responsePayload);

        }
        
        $buildSignature = md5($apikey.'-'.$roundid.'-'.$apisecret);
            if ($buildSignature !== $sign) {
            return;
        }


        if ($bet > 0) {
            $betFloat = $request['bet'] / 100;
            $gameData = json_encode($request);
            $getBalance = $user->balance($currency)->get();
            $bet = number_format($currency->convertUSDToToken($betFloat), 8, '.', '');
            if (config('settings.demo_mode')) {
                $stat = Statistics::where('user', $user->_id)->first();
                if ($stat->data['usd_wager'] > config('settings.demo_mode_max_bet')) {
                        $user->balance($currency)->subtract(floatval($getBalance), Transaction::builder()->message('Demo balance expired')->get());
                        event(new \App\Events\UserNotification($user, 'Demo expired', 'You have reached max. amount of demo play - request to refresh your demo access & balance.'));
                    return;
                }
            }
            if ($bet > $getBalance) {
                return 'no balance';
            }
            $user->balance($currency)->subtract(floatval($bet), Transaction::builder()->meta($roundid)->game($gameid)->get());
        }

        if ($win > 0) {
            $winFloat = $request['win'] / 100;
            $win = number_format($currency->convertUSDToToken($winFloat), 8, '.', '');
            $user->balance($currency)->add(floatval($win), Transaction::builder()->meta($roundid)->game($gameid)->get());
        }

        if ($final === '1') {
            $wagerFloat = $request['totalBet'] / 100 ?? 0;
            $wager = floatval(number_format($currency->convertUSDToToken($wagerFloat), 8, '.', '')) ?? 0;
            $winFloat = $request['totalWin'] / 100 ?? 0;
            $win = floatval(number_format($currency->convertUSDToToken($winFloat), 8, '.', '')) ?? 0;

            $status = 'lose';
            if ($win > $wager) {
                $status = 'win';
            }
            if ($wager > 0) {
                $multi = floatval(number_format(($win / $wager), 2, '.', ''));
            } else {
                $multi = 0;
            }
            $profit = ($win - $wager);

            $game = Game::create([
                'id' => DB::table('games')->count() + 1,
                'user' => $user->_id,
                'game' => $gameid,
                'wager' => $wager,
                'multiplier' => $multi,
                'status' => $status,
                'profit' => $win,
                'server_seed' => $roundid,
                'client_seed' => '-1',
                'nonce' => '-1',
                'data' => [],
                'type' => 'external',
                'currency' => $currency->id(),
            ]);
            
            $thirdpartyGames = Gameslist::cachedList()->where('id', '=', $gameid)->first();
            if($thirdpartyGames) {
            try {
 
            event(new LiveFeedGame($game, '1'));
            Statistics::insert(
                $game->user,
                $game->currency,
                $game->wager,
                $game->multiplier,
                $game->profit,
                'external'
            );

            $multiAllow = 0;
            if ($multi > floatval(1.30) || $multi < floatval(0.9)) {
                $multiAllow = '1';
            }

            if ($wagerFloat > 0.08 && $multiAllow === '1') {
                Challenges::check($gameid, $wagerFloat, $multi, $user->_id);
                Leaderboard::insert($game);

            }
                } catch (\Exception $exception) {
                    Log::notice('error externalcontroller');
                }
            }
        }

        $getBalance = $user->balance($currency)->get();
        $getBalanceUSD = intval($currency->convertTokenToUSD($getBalance) * 100);

        $responsePayload = ['status' => 'ok', 'result' => ['balance' => $getBalanceUSD, 'freegames' => 0]];

        echo json_encode($responsePayload);
    }

    public function methodGetGamesByProvider(Request $request)
    {

        $takeAmount = '12';
        $gameId = $request->id;

        $getProvider = Gameslist::cachedList()->where('id', '=', $gameId)->first()->provider;
        $countGames = Gameslist::cachedList()->where('provider', '=', $getProvider)->count();
        if($countGames < '12') {
            $takeAmount = $countGames;
        }
        $thirdpartyGames = Gameslist::cachedList()->where('provider', '=', $getProvider)->take($takeAmount);

        $games = [];

        foreach ($thirdpartyGames as $game) {
            array_push($games, [
                'ext' => true,
                'name' => $game->name,
                'id' => $game->id,
                'icon' => $game->image_sq,
                'cat' => [$game->category],
                'p' => $game->provider,
                'type' => 'external',
            ]);
        }

        return $games;
    }

    public function methodGetUrlBonusBattle(Request $request)
    {
        $apikey = config('settings.api_key');
            $roomExists = BonusBattles::where('room_id', '=', (int) $request->roomid)->first();
            $userId = auth('sanctum')->user()->_id;
            $stake = $roomExists->stake;
            $status = $roomExists->room_state;

        if (in_array($userId, $roomExists->players)) {
            $url = 'https://api.dk.games/v2/createSession?apikey='.$apikey.'&userid='.$userId.'-'.$request->roomid.'&game='.$request->id.'&mode=real';

            if($status === 'joinable') {
                return APIResponse::reject(2, 'Game has not started yet, please wait till this game starts.');
            }

        $response = Http::get($url);


            $gameslist = (Gameslist::where('id', $request->id)->first());
            $statusCode = $response->status();
            $responseBody = json_decode($response->getBody(), true);
            
            if($statusCode === 200) {
                   return APIResponse::success([
                        'url' => $response['url'],
                        'mode' => true,
                        'id' => $gameslist['id'],
                        'name' => $gameslist['name'],
                        'stake' => $roomExists->stake,
                        'status' => $roomExists->room_state,
                        'image' => $gameslist['image'],
                        'provider' => $gameslist['provider'],
                    ]);
            } else {
                return APIResponse::reject(3, 'Something went wrong loading game.. Try refreshing page.');
            }
        
        } else {  
            return APIResponse::reject(1, 'You are not in this game.');
        }


    }
    public function methodGetUrl(Request $request)
    {
        Log::warning($request);
        $freespins = false;

        if (auth('sanctum')->guest()) {
            $mode = 'demo';
            $currencyId = 'usd';
            $userId = 'guest';
            $name = 'guest';
        } else {
            $name = auth('sanctum')->user()->name;
            $userId = auth('sanctum')->user()->id;
            $currencyId = $request->currency;
            if (auth('sanctum')->user()->freespins > 0) {
                $freespin_slots = \App\Settings::get('category_freespins');
                if (strpos($freespin_slots, $request->id) !== false) {
                    $freespins = true;
                    $user = User::where('_id', $userId)->first();
                }
            }
        }
        if ($request->mode === true && ! auth('sanctum')->guest()) {
            $mode = 'real';
        } else {
            $mode = 'demo';
        }
        $apikey = config('settings.api_key');
        


        //Free Spins session & normal session
        if ($freespins === true) {
            $url = 'https://api.dk.games/v2/createSession?apikey='.$apikey.'&userid='.$userId.'-'.$currencyId.'&game='.$request->id.'&mode=bonus&freespins='.$user->freespins.'&freespins_value=0.5';
            $user->update(['freespins' => 0]);
        } else {
            $url = 'https://api.dk.games/v2/createSession?apikey='.$apikey.'&userid='.$userId.'-'.$currencyId.'&game='.$request->id.'&mode='.$mode.'&nick='.$name;
        }

        $response = Http::get($url);

        $gameslist = (Gameslist::where('id', $request->id)->first());
        $statusCode = $response->status();


        if($statusCode === 200) {

        return APIResponse::success([
            'url' => $response['url'],
            'mode' => ($mode === 'real' ? true : false),
            'id' => $gameslist['id'],
            'name' => $gameslist['name'],
            'image' => $gameslist['image'],
            'provider' => $gameslist['provider'],
        ]);

        } else {
            return APIResponse::reject(1, 'Something went wrong loading game.. Try refreshing page.');
        }

    }
}
