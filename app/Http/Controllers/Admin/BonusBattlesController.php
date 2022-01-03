<?php

namespace App\Http\Controllers\Admin;

use App\Challenges;
use App\Gameslist;
use App\Events\ChallengesNew;
use App\Events\ChallengesRemove;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateChallengeRequest;
use App\Utils\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BonusBattlesAdminController extends Controller
{
    public function get()
    {
        return APIResponse::success(Challenges::get()->toArray());
    }

    public function create(CreateChallengeRequest $request)
    {
        $selectGame = Gameslist::where('id', request('game'))->first();

        $challenge = Challenges::create([
            'game' => request('game'),
            'game_name' => $selectGame->name,
            'game_image' => $selectGame->image,
            'game_provider' => $selectGame->provider,
            'game_category' => $selectGame->category,
            'multiplier' => request('multiplier'),
            'currency' => request('currency'),
            'winners' => [],
            'completed' => 0,
            'minbet' => request('minbet'),
            'sum' => floatval(request('sum')),
            'maxwinners' => request('maxwinners'),
            'expired' => 0,
            'expires' => request('expires') === '%unlimited%' ? Carbon::minValue() : Carbon::createFromFormat('d-m-Y H:i', $request->get('expires')),
        ]);
		
		event(new ChallengesNew($challenge));

        return APIResponse::success();
    }

    public function remove(Request $request)
    {
        Challenges::where('_id', $request->get('id'))->delete();
		event(new ChallengesRemove([$request->get('id')]));

        return APIResponse::success();
    }

    public function removeInactive(Request $request)
    {
		$ids = [];
        foreach (Challenges::get() as $challenge) {
            if (($challenge->expires->timestamp != Carbon::minValue()->timestamp && $challenge->expires->isPast())
                || ($challenge->maxwinners != -1 && $challenge->expired >= $challenge->maxwinners)) {
				array_push($ids, $challenge->_id);
                $challenge->delete();
            }
        }

        return APIResponse::success();
    }
}
