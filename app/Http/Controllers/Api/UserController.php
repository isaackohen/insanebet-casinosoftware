<?php

namespace App\Http\Controllers\Api;

use App\Currency\Currency;
use App\Game as GameResult;
use App\Games\Kernel\Game;
use App\Gameslist;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\AvatarUserRequest;
use App\Http\Requests\Api\ChangePasswordUserRequest;
use App\Http\Requests\Api\ClientSeedChangeUserRequest;
use App\Http\Requests\Api\NameChangeUserRequest;
use App\Http\Requests\Api\SubscriptionUpdateUserRequest;
use App\Investment;
use App\Settings;
use App\Statistics;
use App\TransactionStatistics;
use App\Transaction;
use App\User;
use App\Utils\APIResponse;
use App\VIPLevels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class UserController
{
    public function getUser(Request $request)
    {
        $user = User::where('_id', $request->id)->first();
        if (! $user) {
            return APIResponse::reject(1, 'Unknown user');
        }

        $isOwner = ! auth('sanctum')->guest() && auth('sanctum')->user()->_id === $user->_id;
        if ($isOwner) {
            $tfa = $user->tfa();
            $secret = $tfa->createSecret(160);
        }

        $currencies = [];
        foreach (Currency::all() as $currency) {
            $currencies = array_merge($currencies, [
                $currency->id() => Investment::getUserProfit($currency, Carbon::minValue(), $user),
            ]);
        }

        return APIResponse::success(array_merge([
            'user' => $isOwner ? $user->makeVisible($user->hidden)->makeHidden($user->alwaysHidden)->toArray() : $user->toArray(),
            'isOwner' => $isOwner,
            'showStats' => DB::table('games')->where('user', $user->_id)->where('demo', '!=', true)->first() != null,
            'vipLevel' => $user->vipLevel(),
            'profit' => $currencies,
        ], $isOwner ? [
            'secret' => $secret,
            'qr' => $tfa->getQRText('casino.management', $secret),
        ] : []));
    }

    public function markGameAsFavorite(Request $request)
    {
        $games = auth('sanctum')->user()->favoriteGames ?? [];
        if (in_array($request->id, $games)) {
            unset($games[array_search($request->id, $games)]);
            $games = array_values($games);
        } else {
            array_push($games, $request->id);
        }
        auth('sanctum')->user()->update([
            'favoriteGames' => array_values($games),
        ]);
    }

    public function graph(Request $request)
    {
        $data = [];
        $currency = Currency::find($request->currency);
        $user = User::where('_id', $request->user)->first();

        if ($currency == null || $user == null) {
            return APIResponse::reject(1, 'Invalid parameters');
        }

        if ($request->interval === 'today') {
            $hours = 0;
            for ($i = 0; $i <= 23; $i++) {
                if (Carbon::now()->timestamp < Carbon::today()->addHours($i)->timestamp) {
                    continue;
                }
                $hours++;
            }

            for ($i = 0; $i <= $hours; $i++) {
                array_push($data, [
                    'x' => count($data) + 1,
                    'y' => Investment::getUserProfit($currency, Carbon::minValue(), $user, now()->subHours($hours - $i)),
                ]);
            }
        } else {
            for ($i = 1; $i <= intval($request->interval); $i++) {
                array_push($data, [
                    'x' => count($data) + 1,
                    'y' => Investment::getUserProfit($currency, Carbon::minValue(), $user, now()->subDays(intval($request->interval) - $i)),
                ]);
            }
        }

        return APIResponse::success($data);
    }

    public function games($id)
    {
        $p = [];
        foreach (GameResult::orderByDesc('id')->where('demo', '!=', true)->where('user', $id)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take(15)->get() as $game) {
            if ($game->type === 'external') {
                $getgamename = (Gameslist::where('id', $game->game)->first());
                $image = 'Image/https://games.cdn4.dk/games'.$getgamename->image.'?q=95&mask=ellipse&auto=compress&sharp=10&w=20&h=20&fit=crop&usm=5&fm=png';
                $meta = ['id' => $game->game, 'icon' => $image, 'name' => $getgamename->name, 'category' => [$getgamename->category]];
            } else {
                $meta = Game::find($game->game)->metadata()->toArray();
            }

            array_push($p, [
                'game' => $game->toArray(),
                'metadata' => $meta,
            ]);
        }

        return APIResponse::success($p);
    }

    public function statistics($id)
    {
        $s = [];
        $statistics = Statistics::where('user', $id)->first();

        foreach (Currency::all() as $currency) {
            $var1 = 'bets_'.$currency->id();
            $var2 = 'wins_'.$currency->id();
            $var3 = 'loss_'.$currency->id();
            $var4 = 'wagered_'.$currency->id();

            $s = array_merge($s, [
                $currency->id() => [
                    'bets' => $statistics->data[$var1] ?? 0,
                    'wins' => $statistics->data[$var2] ?? 0,
                    'loss' => $statistics->data[$var3] ?? 0,
                    'wagered' => number_format(($statistics->data[$var4] ?? 0), 8, '.', ''),
                    'wagered_usd' => number_format((($statistics->data[$var4] ?? 0) * $currency->tokenPrice()), 0, '.', ''),
                ],
            ]);
        }

        return APIResponse::success($s);
    }

    public function vip()
    {
        $response = [];
        $statistics = Statistics::where('user', auth('sanctum')->user()->_id)->first();

        $response = array_merge($response, [
            'user' => [
                'viplevel' => $statistics->viplevel ?? 0,
                'vip_progress' => $statistics->vip_progress ?? 0,
                'rakeback' => $statistics->current_rakeback ?? 0,
                'wagered' => $statistics->data['usd_wager'] ?? 0,
            ],
        ]);
        $vips = [];
        foreach (VIPLevels::get() as $vip) {
            $vips = array_merge($vips, [
                $vip->level => [
                    'level' => $vip->level,
                    'name' => $vip->level_name,
                    'start' => $vip->start,
                    'rake_percent' => $vip->rake_percent,
                    'promocode_bonus' => $vip->promocode_bonus,
                    'faucet_bonus' => $vip->faucet_bonus,
                    'fs_bonus' => $vip->fs_bonus,
                    'fs_superspin' => $vip->fs_superspin,
                    'challenges_bonus' => $vip->challenges_bonus,
                ],
            ]);
        }
        $response['vips'] = $vips;

        return APIResponse::success($response);
    }
    /*
    public function investmentHistory()
    {
        $out = [];
        foreach (Investment::where('user', auth('sanctum')->user()->_id)->orderBy('status')->latest()->get() as $investment) {
            array_push($out, [
                'amount' => $investment->amount,
                'share' => $investment->status == 1 ? $investment->disinvest_share : $investment->getRealShare($investment->getProfit(), Investment::getGlobalBankroll(Currency::find($investment->currency))),
                'profit' => $investment->getProfit() <= 0 ? 0 : $investment->getProfit(),
                'status' => $investment->status,
                'id' => $investment->_id,
                'currency' => $investment->currency,
            ]);
        }

        return APIResponse::success($out);
    }

    public function investmentStats()
    {
        $currency = auth('sanctum')->user()->clientCurrency();
        $userBankroll = Investment::getUserBankroll($currency, auth('sanctum')->user());
        $globalBankroll = Investment::getGlobalBankroll($currency);

        $userBankrollShare = 0;
        foreach (Investment::where('user', auth('sanctum')->user()->_id)->where('currency', $currency->id())->where('status', 0)->get() as $investment) {
            $userBankrollShare += $investment->getRealShare($investment->getProfit(), $globalBankroll);
        }

        return APIResponse::success([
            'your_bankroll' => auth('sanctum')->user()->getInvestmentProfit($currency, false),
            'your_bankroll_percent' => $userBankroll == 0 || $globalBankroll == 0 ? 0 : $userBankroll / $globalBankroll * 100,
            'your_bankroll_share' => $userBankrollShare,
            'investment_profit' => auth('sanctum')->user()->getInvestmentProfit($currency, true, false),
            'site_bankroll' => $globalBankroll,
            'site_profit' => Investment::getSiteProfitSince($currency, Carbon::minValue()),
        ]);
    }

    public function subscriptionUpdate(SubscriptionUpdateUserRequest $request)
    {
        auth('sanctum')->user()->updatePushSubscription(
            $request->endpoint,
            $request->publicKey,
            $request->authToken,
            $request->contentEncoding
        );
        if (auth('sanctum')->user()->notification_bonus != true) {
            auth('sanctum')->user()->update([
                'notification_bonus' => true,
            ]);
            auth('sanctum')->user()->balance(auth('sanctum')->user()->clientCurrency())->add(floatval(auth('sanctum')->user()->clientCurrency()->option('referral_bonus')), Transaction::builder()->message('Referral bonus')->get());
        }

        return APIResponse::success();
    }
    */

    public function find(Request $request)
    {
        $user = User::where('name', 'like', "%{$request->name}%")->first();
        if ($user == null) {
            return APIResponse::reject(1, 'Unknown username');
        }

        return APIResponse::success(['id' => $user->_id]);
    }

    public function ignore(Request $request)
    {
        $user = User::where('name', 'like', "%{$request->name}%")->first();
        if ($user == null || $user->_id === auth('sanctum')->user()->_id) {
            return APIResponse::reject(1, 'Unknown username');
        }

        $ignore = auth('sanctum')->user()->ignore ?? [];
        if (in_array($user->_id, $ignore)) {
            return APIResponse::reject(2, 'Already ignored');
        }
        array_push($ignore, $user->_id);

        auth('sanctum')->user()->update(['ignore' => $ignore]);

        return APIResponse::success(['id' => $user->_id]);
    }

    public function unignore(Request $request)
    {
        $user = User::where('name', 'like', "%{$request->name}%")->first();
        if ($user == null) {
            return APIResponse::reject(1, 'Unknown username');
        }

        $ignore = auth('sanctum')->user()->ignore ?? [];
        if (! in_array($user->_id, $ignore)) {
            return APIResponse::reject(2, 'User is not ignored');
        }
        $index = array_search($user->_id, $ignore);
        unset($ignore[$index]);

        auth('sanctum')->user()->update(['ignore' => $ignore]);

        return APIResponse::success(['id' => $user->_id]);
    }

    public function changePassword(ChangePasswordUserRequest $request)
    {
        if (! auth('sanctum')->user()->validate2FA(false)) {
            return APIResponse::invalid2FASession();
        }
        auth('sanctum')->user()->reset2FAOneTimeToken();

        if (! Hash::check($request->old, auth('sanctum')->user()->password)) {
            return APIResponse::reject(1, 'Invalid old password');
        }

        auth('sanctum')->user()->update(['password' => Hash::make($request->new)]);

        return APIResponse::success();
    }

    public function updateEmail(Request $request)
    {
        if (filter_var($request->email, FILTER_VALIDATE_EMAIL) === false) {
            return APIResponse::reject(1, 'Invalid email');
        }

        if (! auth('sanctum')->user()->validate2FA(false)) {
            return APIResponse::invalid2FASession();
        }
        auth('sanctum')->user()->reset2FAOneTimeToken();

        auth('sanctum')->user()->update(['email' => $request->email]);

        return APIResponse::success();
    }

    public function clientSeedChange(ClientSeedChangeUserRequest $request)
    {
        auth('sanctum')->user()->update([
            'client_seed' => $request->client_seed,
        ]);

        return APIResponse::success();
    }

    public function nameChange(NameChangeUserRequest $request)
    {
        if (! auth('sanctum')->user()->validate2FA(false)) {
            return APIResponse::invalid2FASession();
        }
        auth('sanctum')->user()->reset2FAOneTimeToken();

        $history = auth('sanctum')->user()->name_history;
        array_push($history, [
            'time' => Carbon::now(),
            'name' => $request->name,
        ]);

        auth('sanctum')->user()->update([
            'name' => $request->name,
            'name_history' => $history,
        ]);

        return APIResponse::success();
    }

    public function twofaValidate(Request $request)
    {
        if ((auth('sanctum')->user()->tfa_enabled ?? false) == false) {
            return APIResponse::reject(1, '2FA is disabled');
        }
        $client = auth('sanctum')->user()->tfa();
        if (request('code') == null || $client->verifyCode(auth('sanctum')->user()->tfa_sec, request('code')) !== true) {
            return APIResponse::reject(2, 'Invalid 2fa code');
        }

        auth('sanctum')->user()->update([
            'tfa_onetime_key' => now()->addSeconds(15),
            'tfa_persistent_key' => now()->addDays(1),
        ]);

        return APIResponse::success();
    }

    public function twofaEnable(Request $request)
    {
        if (auth('sanctum')->user()->tfa_enabled ?? false) {
            return APIResponse::reject(1, 'Hacking attempt');
        }
        $client = auth('sanctum')->user()->tfa();

        if (request('2faucode') == null || $client->verifyCode(request('2facode'), request('2faucode')) !== true) {
            return APIResponse::reject(2, 'Invalid 2fa code');
        }

        auth('sanctum')->user()->update([
            'tfa_enabled' => true,
            'tfa_sec' => request('2facode'),
        ]);

        return APIResponse::success();
    }

    public function twofaDisable(Request $request)
    {
        if (! auth('sanctum')->user()->validate2FA(false)) {
            return APIResponse::invalid2FASession();
        }
        auth('sanctum')->user()->update([
            'tfa_enabled' => false,
            'tfa' => null,
        ]);
        auth('sanctum')->user()->reset2FAOneTimeToken();

        return APIResponse::success();
    }

    public function privacy_toggle(Request $request)
    {
        auth('sanctum')->user()->update([
            'private_profile' => auth('sanctum')->user()->private_profile ? false : true,
        ]);

        return APIResponse::success();
    }

    public function privacy_bets_toggle(Request $request)
    {
        auth('sanctum')->user()->update([
            'private_bets' => auth('sanctum')->user()->private_bets ? false : true,
        ]);

        return APIResponse::success();
    }

    public function avatar(AvatarUserRequest $request)
    {
        $path = auth('sanctum')->user()->_id.time();
        $request->image->move(public_path('img/avatars'), $path.'.'.$request->image->getClientOriginalExtension());

        $img = Image::make(public_path('img/avatars/'.$path.'.'.$request->image->getClientOriginalExtension()));
        $img->resize(100, 100);
        $img->encode('jpg', 75);
        $img->save(public_path('img/avatars/'.$path.'.jpg'), 75, 'jpg');

        auth('sanctum')->user()->update([
            'avatar' => '/img/avatars/'.$path.'.jpg',
        ]);

        return APIResponse::success();
    }

    public function callbackTelegram(Request $request, $id)
    {
        $user = User::where('_id', $id)->first();
        $tglink = Settings::where('name', 'telegram_link')->first()->value;
        $checkcount = User::where('telegram', $request->id)->count();
        if ($checkcount == 0 && $user != null) {
            $user->update(['telegram' => $request->id]);
        }

        return redirect()->away($tglink);
    }
}
