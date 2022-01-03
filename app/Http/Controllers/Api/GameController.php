<?php

namespace App\Http\Controllers\Api;

use App\Currency\Currency;
use App\DisabledGamesReff;
use App\Game as GameResult;
use App\Games\Kernel\Data;
use App\Games\Kernel\Extended\ExtendedGame;
use App\Games\Kernel\Game;
use App\Games\Kernel\Module\ModuleSeeder;
use App\Games\Kernel\ProvablyFairResult;
use App\Gameslist;
use App\Http\Requests\Api\FinishGameRequest;
use App\Settings;
use App\User;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameController
{
    /**
     * @param Request $request
     * @return array|array[]
     */
    public function play(Request $request)
    {
        $game = Game::find($request->api_id);
        if ($game == null) {
            return ['code' => -3, 'message' => 'Unknown API game id'];
        }
        if ($game->isDisabled()) {
            return ['code' => -5, 'message' => 'Game is disabled'];
        }
        if (auth('sanctum')->user() != null && ! $game->ignoresMultipleClientTabs() && DB::table('games')->where('game', $request->api_id)->where('user', auth('sanctum')->user()->_id)->where('status', 'in-progress')->count() > 0) {
            return ['code' => -8, 'message' => 'Game already has started'];
        }

        if (auth('sanctum')->user() == null && ! $request->demo) {
            return ['code' => -2, 'message' => 'Not authorized'];
        }
        if (auth('sanctum')->user() == null && $request->demo) {
            return ['code' => -2, 'message' => 'Not authorized'];
        }
        if (! $game->usesCustomWagerCalculations() && floatval($request->bet) < floatval(Settings::get('min_bet') / Currency::find($request->currency)->tokenPrice())) {
            return ['code' => -1, 'message' => 'Invalid wager value'];
        }
        if (! $game->usesCustomWagerCalculations() && floatval($request->bet) > floatval(Settings::get('max_bet') / Currency::find($request->currency)->tokenPrice())) {
            return ['code' => -9, 'message' => 'Invalid wager value'];
        }
        if (auth('sanctum')->user() != null && (auth('sanctum')->user()->balance(Currency::find($request->currency))->demo($request->demo)->get() < floatval($request->bet))) {
            return ['code' => -4, 'message' => 'Not enough money'];
        }
        if (auth('sanctum')->user()->balance(Currency::find($request->currency))->get() < floatval($request->bet)) {
            return ['code' => -4, 'message' => 'Not enough money'];
        }

        $data = new Data(auth('sanctum')->user(), [
            'api_id' => $request->api_id,
            'bet' => $request->bet,
            'currency' => $request->currency,
            'demo' => $request->demo,
            'quick' => $request->quick,
            'data' => (array) $request->data,
        ]);

        /*

        if (auth('sanctum')->user() != null && auth('sanctum')->user()->referral != null && auth('sanctum')->user()->games() >= floatval(Settings::get('referrer_activity_requirement', 100))) {
            $referrer = \App\User::where('_id', $this->user->referral)->first();
            $referrals = $referrer->referral_wager_obtained ?? [];
            if (! in_array(auth('sanctum')->user()->_id, $referrals)) {
                array_push($referrals, auth('sanctum')->user()->_id);
                $referrer->update(['referral_wager_obtained' => $referrals]);
                $referrer->balance(Currency::find('btc'))->add(floatval(Currency::find('btc')->option('referral_bonus')), \App\Transaction::builder()->message('Active referral bonus')->get());
            }
        }

        if (auth('sanctum')->user() != null && auth('sanctum')->user()->vipLevel() > 0 && auth('sanctum')->user()->vip_discord_notified == null) {
            auth('sanctum')->user()->notify(new \App\Notifications\VipDiscordNotification());
            auth('sanctum')->user()->update(['vip_discord_notified' => true]);
        }*/

        return $game->process($data);
    }

    /**
     * @param Request $request
     * @return array|array[]
     */
    public function turn(Request $request)
    {
        $game = GameResult::where('_id', $request->id)->first();
        if ($game == null) {
            return ['code' => 1, 'message' => 'Invalid game id'];
        }
        if ($game->status != 'in-progress') {
            return ['code' => 2, 'message' => 'Game is finished'];
        }

        $apiGame = Game::find($game->game);
        if (! ($apiGame instanceof ExtendedGame)) {
            return ['code' => 3, 'message' => 'Unsupported game operation'];
        }

        $server_seed = (new ModuleSeeder($apiGame, $game->demo, null, $game))->find(function (ProvablyFairResult $result) use ($apiGame, $game, $request) {
            return $apiGame->isLoss($result, $game, (array) $request->data);
        }, $game->server_seed);

        $game->update([
            'server_seed' => $server_seed,
            'data' => [
                'turn' => $game->data['turn'] + 1,
                'history' => $game->data['history'],
                'user_data' => $game->data['user_data'],
                'game_data' => $game->data['game_data'],
            ],
        ]);

        $turnData = $apiGame->turn($game, (array) $request->data)->toArray();
        switch ($turnData['type']) {
            case 'fail':
                $apiGame->setTurn($game, $apiGame->getTurn($game) - 1);
                break;
            case 'lose':
                $game->update(['status' => 'lose']);
                $apiGame->finish($game);
                break;
            case 'finish':
                $game->update([
                    'status' => $game->profit == 0 ? 'lose' : 'win',
                ]);
                $apiGame->finish($game);
                break;
        }

        return APIResponse::success(array_merge($turnData, [
            'game' => $game->makeHidden('server_seed')->makeHidden('nonce')->makeHidden('data')->toArray(),
            'turn' => $apiGame->getTurn($game),
        ]));
    }

    /**
     * @param Request $request
     * @return array|array[]
     */
    public function pokerEndpoint(Request $request)
    {
        Log::notice($request);
    }

    /**
     * @param Request $request
     * @return array|array[]
     */
    public function finish(FinishGameRequest $request)
    {
        $game = GameResult::where('_id', $request->id)->first();
        if ($game == null) {
            return APIResponse::reject(1, 'Invalid game id');
        }
        if ($game->status != 'in-progress') {
            return APIResponse::reject(2, 'Game is finished');
        }

        $apiGame = Game::find($game->game);
        if (! ($apiGame instanceof ExtendedGame)) {
            return APIResponse::reject(3, 'Unsupported game operation');
        }

        $apiGame->finish($game);

        return APIResponse::success([
            'game' => $game->toArray(),
        ]);
    }

    /**
     * @param Request $request
     * @param $apiId
     * @return array|array[]
     */
    public function data(Request $request, $apiId)
    {
        $game = Game::find($apiId);
        if ($game == null) {
            return APIResponse::reject(-3, 'Unknown API game id');
        }

        return APIResponse::success($game->data());
    }

    public function restore(Request $request)
    {
        $game = Game::find($request->api_id);
        if ($game == null) {
            return ['code' => 1, 'message' => 'Unknown API game id'];
        }
        if ($game->isDisabled()) {
            return ['code' => 2, 'message' => 'Game is disabled'];
        }

        $latestGame = GameResult::orderBy('id', 'desc')
                ->where('game', $game->metadata()->id())
                ->where('user', optional(auth('sanctum')->user())->_id)
                ->where('status', 'in-progress')
                ->first();

        if (! $latestGame) {
            return ['code' => 3, 'message' => 'Nothing to restore'];
        }

        return APIResponse::success([
            'game' => $latestGame->makeHidden('server_seed')->makeHidden('nonce')->makeHidden('data')->toArray(),
            'history' => $latestGame->data['history'],
            'user_data' => $latestGame->data['user_data'],
        ]);
    }

    /**
     * @param $id
     * @return array|array[]
     */
    public function info($id)
    {
        $game = GameResult::where('_id', $id)->first();
        if ($game == null) {
            return APIResponse::reject(1, 'Unknown game id');
        }
        if ($game->status === 'in-progress' || $game->status === 'cancelled') {
            return APIResponse::reject(2, 'Game is not finished');
        }

        if ($game->type === 'external') {
            $getgamename = Gameslist::cachedList()->where('id', '=', $game->game)->first();
            $image = 'Image/https://cdn2.davidkohen.com/v1/icons'.$getgamename->image.'?q=95&mask=ellipse&auto=compress&sharp=10&w=20&h=20&fit=crop&fm=png';
            $metadata = ['id' => $game->game, 'icon' => $image, 'name' => $getgamename->name, 'category' => [$getgamename->category]];
        } else {
            $metadata = Game::find($game->game)->metadata()->toArray();
        }

        return APIResponse::success([
            'metadata' => $metadata,
            'info' => $game->toArray(),
            'user' => User::where('_id', $game->user)->first()->toArray(),
        ]);
    }

    public function pushBullData(Request $request)
    {
        if (User::getIp() !== '127.0.0.1' && User::getIp() !== config('settings.server_ip')) {
            return APIResponse::reject(1, 'Access to this API request is restricted for '.User::getIp());
        }
        Game::find('bullvsbear')->state()->pushData($request->data)->sendDataUpdateEvent();

        return APIResponse::success();
    }
}
