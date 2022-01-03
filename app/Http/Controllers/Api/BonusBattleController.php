<?php

namespace App\Http\Controllers\Api;

use App\Challenges;
use App\BonusBattles;
use App\Currency\Currency;
use App\Promocode;
use App\Settings;
use App\Statistics;
use App\User;
use App\Transaction;
use App\TransactionStatistics;
use App\Utils\APIResponse;
use App\Gameslist;
use App\VIPLevels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Events\BonusBattleNew;
use App\Events\ChallengesRemove;
use App\Events\UserNotification;
use App\Events\BonusBattleStarted;

use App\Events\BonusBattleUpdate;



class BonusBattleController
{
	
    public function getBonusBattle(Request $request)
    {
		$page = $request->page ?? null;
        $depth = $request->depth ?? 12;
        if($request->status === 'completed') {
			$battles = BonusBattles::where('room_state', 'completed')->orWhere('room_state', 'cancelled')->latest()->get()->makeVisible(['winner_name'])->makeVisible(['winner_id'])->makeVisible(['winner_amount'])->makeVisible(['player_balance_1'])->makeVisible(['player_balance_2'])->makeVisible(['player_balance_3'])->makeVisible(['player_balance_4'])->toArray();
        } else if($request->status === 'active') {
			$battles = BonusBattles::where('room_state', 'active')->orWhere('room_state', 'started')->latest()->get()->reverse()->toArray();
		} else if($request->status === 'joinable') {
			$battles = BonusBattles::where('room_state', 'joinable')->latest()->get()->reverse()->toArray();
        } else if($request->status === 'withme') {
			$userBattles = BonusBattles::where('player_id_1', auth('sanctum')->user()->_id)->orWhere('player_id_2', auth('sanctum')->user()->_id)->orWhere('player_id_3', auth('sanctum')->user()->_id)->orWhere('player_id_4', auth('sanctum')->user()->_id)->get()->reverse();
			$battles = [];
			foreach($userBattles as $battle) {
				if($battle->room_state === 'completed' || $battle->room_state === 'cancelled') {
					$lobby = $battle->makeVisible(['winner_name'])->makeVisible(['winner_id'])->makeVisible(['winner_amount'])->makeVisible(['player_balance_1'])->makeVisible(['player_balance_2'])->makeVisible(['player_balance_3'])->makeVisible(['player_balance_4']);
					array_push($battles, $lobby);
				} else {
					array_push($battles, $battle);
				}
			}
		}
		$response = [];
		$data = [];
        $data = array_merge($data, $battles);
        array_push($response, [
            'count' => count($data),
            'page' => ($page === null ? 'all' : $page),
            'battles' => $page === null ? $data : (array_slice($data, ($page * $depth), $depth)),
        ]);
		return APIResponse::success($response);
    }

    public function getBonusBattleRoom(Request $request)
    {
		$room = BonusBattles::where('room_id', '=', (int) $request->room)->first();
		if(!$room) return APIResponse::reject(1, 'Room does not exist.');
		if($room->room_state === 'completed' || $room->room_state === 'cancelled') {
			return APIResponse::success($room->makeVisible(['winner_name'])->makeVisible(['winner_id'])->makeVisible(['winner_amount'])->makeVisible(['player_balance_1'])->makeVisible(['player_balance_2'])->makeVisible(['player_balance_3'])->makeVisible(['player_balance_4'])->toArray());
		} else {
			return APIResponse::success($room->toArray());
		}
    }

    public function claim(Request $request)
    {
        Log::notice($request);
        $roomId = $request->battleroom;
        $selectRoom = BonusBattles::where('room_id', $roomId)->first();

        $currency = Currency::find($selectRoom->currency);
        $amount = $currency->convertUSDToToken($selectRoom->winner_amount);

        if ($selectRoom->winner_id !== auth('sanctum')->user()->_id) {
            return APIResponse::reject(1, 'Not winning player.');
        }

        if ($selectRoom->claimed === true) {
            return APIResponse::reject(1, 'Not winning player.');
        }


        auth('sanctum')->user()->balance(Currency::find(auth('sanctum')->user()->clientCurrency()->id()))->add($amount, Transaction::builder()->message('Claimed Bonus Battle Win')->get());
        $selectRoom->update([
            'claimed' => true
        ]);

        return APIResponse::success();
    }

    public function join(Request $request)
    {
        //Log::notice($request);
        $selectGame = Gameslist::where('id', request('game'))->first();
        $roomId = $request->battleroom;
        $selectRoom = BonusBattles::where('room_id', $roomId)->first();


        $currency = Currency::find($selectRoom->currency);
        $amount = $currency->convertUSDToToken($selectRoom->stake);

        if (auth('sanctum')->user()->balance($currency)->get() < floatval($amount)) {
            return APIResponse::reject(1, 'Not enough money.');
        }
        if (in_array(auth('sanctum')->user()->_id, $selectRoom->players)) {
            return APIResponse::reject(2, 'Already activated');
        }
        if ($selectRoom->players_active >= $selectRoom->players_max) {
            return APIResponse::reject(3, 'This bonus battle is full.');
        }
        if ($selectRoom->room_state !== 'joinable') {
            return APIResponse::reject(4, 'This bonus battle is full.');
        }

        auth('sanctum')->user()->balance(Currency::find(auth('sanctum')->user()->clientCurrency()->id()))->subtract($amount, Transaction::builder()->message('Joined Bonus Battle')->get());

        $players = $selectRoom->players;
        array_push($players, auth('sanctum')->user()->_id);

        //array_fill_keys($players, 20000);

        $player_count = $selectRoom->players_active + 1;

        foreach($selectRoom->players as $player) {
            $userSearch = User::where('_id', $player)->first();
            event(new UserNotification($userSearch, 'Bonus Battle Update - ID'.$selectRoom->room_id, 'Player has joined your bonus battle.'));
        }

        $selectRoom->update([
            'players' => $players,
            'player_id_'.$player_count => auth('sanctum')->user()->_id,
            'player_balance_'.$player_count => $selectRoom->stake,
            'player_final_'.$player_count => 'waiting',
            'player_name_'.$player_count => auth('sanctum')->user()->name,
            'players_active' => $player_count
        ]);

        //Activate game mode when filled up
        if($selectRoom->players_active >= $selectRoom->players_max) {
            $selectRoom->update([
                'room_state' => 'started',
                'expires' => Carbon::now()->addMinutes(2),
            ]);

            foreach($selectRoom->players as $player) {
                $userSearch = User::where('_id', $player)->first();
                event(new UserNotification($userSearch, 'Bonus Battle Update', 'Bonus Battle has started, you have 2 minutes to start & buy bonus for '.$selectRoom->stake.'$ on '.$selectRoom->g_name.'.'));
                event(new BonusBattleStarted($userSearch, $selectRoom->room_id, $selectRoom->game));
            }
        } 
		event(new BonusBattleUpdate($selectRoom));
        return APIResponse::success();
    }


    public function create(Request $request)
    {
        $selectGame = Gameslist::where('id', request('game'))->first();
        $roomId = BonusBattles::count() + 1 + 200;

        $currency = Currency::find(request('currency'));
        $amount = $currency->convertUSDToToken(request('stake'));

        if (auth('sanctum')->user()->balance($currency)->get() < floatval($amount)) {
            return APIResponse::reject(1, 'Not enough balance');
        }
        if (request('players_max') < "2" || request('players_max') > "4") {
            return APIResponse::reject(2, 'Incorrect amount of players.');
        }

        if (request('stake') < "10") {
            return APIResponse::reject(2, 'Incorrect stake amount.');
        }


        /*
        if(request('game') === 'p0_p0-sweet-bonanza-xmas' OR 'p0_p0-gates-of-olympus') {
            return APIResponse::reject(4, 'Incorrect game amount.');
        }
        */
        if ($currency->id() === 'local_bonus' || $currency->id() === 'bonus' || $currency->id() === 'BONUS') {
            return APIResponse::reject(3, 'Bonus Battle cannot be played with BONUS currency.');
        }

 


        $bonusbattle = BonusBattles::create([
            'game' => request('game'),
            'g_name' => $selectGame->name,
            'g_img' => $selectGame->image,
            'g_prov' => $selectGame->provider,
            'g_cat' => $selectGame->category,
            'claimed' => false,
            'players_max' => request('players_max'),
            'players_counter' => request('players_max'),
            'players_active' => 1,
            'players' => [],
            'players_balance' => [],
            'player_id_1' => auth('sanctum')->user()->_id,
            'player_balance_1' => request('stake'),
            'player_final_1' => 'waiting',
            'player_name_1' => auth('sanctum')->user()->name,
            'winner' => null,
            'stake' => request('stake'),
            'currency' => $currency->id(),
            'room_id' => $roomId,
            'room_state' => 'joinable',
            'tx_ids' => [],
            'expires' => Carbon::now()->addMinutes(10),
        ]);

        auth('sanctum')->user()->balance(Currency::find($currency->id()))->subtract($amount, Transaction::builder()->message('Bonus battle creation')->get());

        $selectRoom = BonusBattles::where('room_id', $roomId)->first();
        $players = $selectRoom->players;
        array_push($players, auth('sanctum')->user()->_id);

        //array_fill_keys($players, 20000);
        $selectRoom->update([
            'players' => $players,
        ]);

        event(new BonusBattleNew($bonusbattle));

        return APIResponse::success();
    }

}
 