<?php

namespace App\Http\Controllers\Api;

use App\Currency\Currency;
use App\Game as GameResult;
use App\Games\Kernel\Game;
use App\Games\Kernel\Module\General\HouseEdgeModule;
use App\Gameslist;
use App\Leaderboard;
use App\Providers;
use App\Settings;
use App\User;
use App\Utils\APIResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataController
{

    public static function GameResultAll($count)
    {
        $cachedList = Cache::get('GameResultAll'.$count);

        if (! $cachedList) {
            $cachedList = GameResult::latest()->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($count)->get()->reverse();
            Cache::put('GameResultAll'.$count, $cachedList, Carbon::now()->addMinutes(1));
        }

        return $cachedList;
    }

    public static function GameResultMine($count, $mine)
    {
        $cachedList = Cache::get('GameResultMine'.$count.$mine);

        if (! $cachedList) {
            $cachedList = GameResult::latest()->where('demo', '!=', true)->where('user', $mine)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($count)->get()->reverse();
            Cache::put('GameResultMine'.$count.$mine, $cachedList, Carbon::now()->addMinutes(1));
        }
        return $cachedList;
    }

    public static function GameResultLuckyWins($count)
    {
        $cachedList = Cache::get('GameResultLuckyWins'.$count);

        if (! $cachedList) {
            $cachedList = GameResult::latest()->where('multiplier', '>=', 10)->where('demo', '!=', true)->where('user', '!=', null)->where('status', 'win')->take($count)->get()->reverse();
            Cache::put('GameResultLuckyWins'.$count, $cachedList, Carbon::now()->addMinutes(1));
        }
        return $cachedList;
    }

    public static function GameResultHighResult($count)
    {
        $cachedList = Cache::get('GameResultHighResult'.$count);

        if (! $cachedList) {
            $cachedList = GameResult::latest()->where('demo', '!=', true)->where('user', '!=', null)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($count)->get()->reverse();
            Cache::put('GameResultHighResult'.$count, $cachedList, Carbon::now()->addMinutes(1));
        }
        return $cachedList;
    }
    public function latestGames(Request $request)
    {
        //Disabled

        $result = [];
        switch ($request->type) {
            case 'mine':
                $userID = auth('sanctum')->user()->_id;
                $games = self::GameResultMine($request->count, $userID);
                break;
            case 'all':
                $games = self::GameResultAll($request->count);
                //return [];
                break;
            case 'lucky_wins':
            //Disabled
            return [];
                $games = self::GameResultLuckyWins($request->count);
                break;
            case 'high_rollers':
            //Disabled
            return [];


                $hrResult = [];
                $games = self::GameResultHighResult($request->count);
                foreach ($games as $game) {
                    if ($game->wager < floatval(Settings::get('high_roller_requirement') / \App\Currency\Currency::find($game->currency)->tokenPrice())) {
                        continue;
                    }
                    array_push($hrResult, $game);
                }
                $games = $hrResult;
                break;
        }

        foreach ($games as $game) {
            if ($game->type === 'external') {
                $getGame = Gameslist::cachedList()->where('id', '=', $game->game)->first();
                $getGameName = $getGame->name ?? $game->game;
                $getGameImage = $getGame->image ?? $game->game;
                $getGameCategory = $getGame->category ?? 'slots';
                $image = 'Image/https://cdn2.davidkohen.com/v1/icons'.$getGameImage.'?q=95&mask=ellipse&auto=compress&sharp=10&w=20&h=20&fit=crop&usm=5&fm=png';
                $meta = ['id' => $game->game, 'icon' => $image, 'name' => $getGameName, 'category' => [$getGameCategory]];
                array_push($result, [
                    'game' => $game->toArray(),
                    'user' => User::where('_id', $game->user)->first()->toArray(),
                    'metadata' => $meta,
                ]);
            } else {
                array_push($result, [
                    'game' => $game->toArray(),
                    'user' => User::where('_id', $game->user)->first()->toArray(),
                    'metadata' => Game::find($game->game)->metadata()->toArray(),
                ]);
            }
        }

        return APIResponse::success($result);
    }

    public function notifications(Request $request)
    {
        return APIResponse::success(array_merge(\App\GlobalNotification::get()->toArray(), config('app.debug') && ! Str::contains($request->url(), 'localhost') ? [[
            '_id' => '-1',
            'icon' => 'fad fa-exclamation-triangle',
            'text' => 'Debug',
        ]] : []));
    }

    public function providers(Request $request)
    {
        $page = $request->page ?? null;
        $depth = $request->depth ?? 36;
        $data = Providers::get();
        $providers = [];
        foreach ($data as $c) {
            $providers[$c->provider] = [
                'name' => $c->provider,
                'img' => 'https://provider.cdn4.dk/gameproviders/'.$c->provider.'.png',
            ];
        }
        $response = [];
        $data = array_values($providers);
        array_push($response, [
            'count' => count($data),
            'page' => ($page === null ? 'all' : $page),
            'providers' => $page === null ? $data : (array_slice($data, ($page * $depth), $depth)),
        ]);

        return APIResponse::success($response);
    }

    public function categories()
    {
        $data = Gameslist::optimizedList();
        $categories = [];
        foreach ($data as $c) {
            $categories[$c->cat] = [
                'name' => $c->cat,
            ];
        }
        $data = array_values($categories);

        return APIResponse::success($data);
    }

    public function games(Request $request)
    {
        $text = $request->text ?? null;
        $provider = $request->provider ?? null;
        $category = $request->category ?? null;
        $subcategory = $request->subcategory ?? null;
        $page = $request->page ?? null;
        $depth = $request->depth ?? 36;
        $data = [];
        if ($subcategory) {
            foreach ($subcategory as $sub) {
                $data = array_merge($data, Gameslist::optimizedList($sub));
                if ($category) {
                    array_push($category, $sub);
                }
            }
        }
        if ($category || (! $subcategory && ! $category)) {
            $data = array_merge($data, Gameslist::optimizedList());
        }
        $search = collect($data)->filter(function ($item) use ($text, $provider, $category) {
            if ($text && ! $provider && ! $category) {
                if ((stripos($item->name, $text) !== false) || (stripos($item->p, $text) !== false)) {
                    return true;
                }
            }
            if ($text && $provider && ! $category) {
                foreach ($provider as $prov) {
                    if ((stripos($item->name, $text) !== false) && (stripos($item->p, $prov) !== false)) {
                        return true;
                    }
                }
            }
            if ($provider && ! $text && ! $category) {
                foreach ($provider as $prov) {
                    if (stripos($item->p, $prov) !== false) {
                        return true;
                    }
                }
            }
            if ($category && ! $text && ! $provider) {
                foreach ($category as $cat) {
                    if (stripos($item->cat, $cat) !== false) {
                        return true;
                    }
                }
            }
            if ($category && $text && ! $provider) {
                foreach ($category as $cat) {
                    if ((stripos($item->cat, $cat) !== false) && ((stripos($item->name, $text) !== false) || (stripos($item->p, $text) !== false))) {
                        return true;
                    }
                }
            }
            if ($category && $text && $provider) {
                foreach ($provider as $prov) {
                    foreach ($category as $cat) {
                        if ((stripos($item->cat, $cat) !== false) && (stripos($item->name, $text) !== false) && (stripos($item->p, $prov) !== false)) {
                            return true;
                        }
                    }
                }
            }
            if ($category && $provider && ! $text) {
                foreach ($provider as $prov) {
                    foreach ($category as $cat) {
                        if ((stripos($item->cat, $cat) !== false) && (stripos($item->p, $prov) !== false)) {
                            return true;
                        }
                    }
                }
            }
            if (! $text && ! $provider && ! $category) {
                return true;
            }

            return false;
        });
        $result = array_values($search->all());
        $response = $result;
        $games = [];
        array_push($games, [
            'count' => count($result),
            'page' => ($page === null ? 'all' : $page),
            'games' => $page === null ? $result : (array_slice($result, ($page * $depth), $depth)),
        ]);
        $response = $games;

        return APIResponse::success($response);
    }

    public function currencies(Request $request)
    {
        return APIResponse::success(Currency::toCurrencyArray(Currency::all()));
    }

    public function leaderboard(Request $request)
    {
        if ($request->currency == 'usd') {
            return APIResponse::success(Leaderboard::getLeaderboardByUsd($request->positions, $request->type, $request->orderBy));
        }
        $currency = Currency::find($request->currency ?? '');
        if (! $currency) {
            return APIResponse::reject(2, 'Invalid currency');
        }

        return APIResponse::success(Leaderboard::getLeaderboard($request->positions, $request->type, $currency, $request->orderBy));
    }

    public function gameFind(Request $request)
    {
        $game = Game::where('id', intval($request->id))->first();
        if ($game == null) {
            return APIResponse::reject(1, 'Unknown ID '.$request->id);
        }

        return APIResponse::success([
            'id' => $game->_id,
            'game' => $game->game,
        ]);
    }
}
