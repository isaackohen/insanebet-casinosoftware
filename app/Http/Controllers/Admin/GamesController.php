<?php

namespace App\Http\Controllers\Admin;

use App\DisabledGame;
use App\Game as GameResult;
use App\Games\Kernel\Game;
use App\Gameslist;
use App\Http\Controllers\Controller;
use App\Providers;
use App\Utils\APIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GamesController extends Controller
{
    public function toggle(Request $request)
    {
        if (DisabledGame::where('name', request('name'))->first() == null) {
            DisabledGame::create(['name' => request('name')]);
            (new \App\ActivityLog\DisableGameActivity())->insert(['state' => true, 'api_id' => request('name')]);

            Cache::put('disabledGame:'.\request('name'), true);
        } else {
            DisabledGame::where('name', request('name'))->delete();
            (new \App\ActivityLog\DisableGameActivity())->insert(['state' => false, 'api_id' => request('name')]);

            Cache::put('disabledGame:'.\request('name'), false);
        }
        Artisan::call('optimize:clear');
        Artisan::call('cache:clear');

        return APIResponse::success();
    }

    public function extToggle(Request $request)
    {
        $game = Gameslist::where('_id', \request('id'))->first();
        $game->update([
            'd' => ($game->d === 1) ? 0 : 1,
        ]);
        Artisan::call('optimize:clear');
        Artisan::call('cache:clear');

        return APIResponse::success();
    }

    public function settings()
    {
        return APIResponse::success([
            [
                'name' => 'category_popular',
                'value' => Settings::get('category_popular'),
            ],
            [
                'name' => 'category_bonus',
                'value' => Settings::get('category_bonus'),
            ],
            [
                'name' => 'category_new',
                'value' => Settings::get('category_new'),
            ],
            [
                'name' => 'category_gameshows',
                'value' => Settings::get('category_gameshows'),
            ],
            [
                'name' => 'category_cardgames',
                'value' => Settings::get('category_cardgames'),
            ],
            [
                'name' => 'category_featured',
                'value' => Settings::get('category_featured'),
            ],
        ]);
    }

    public function games(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length'); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value'] ?? null; // Search value

        // Total records
        $totalRecords = Gameslist::where('d', '!=', '1')->select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = Gameslist::where('d', '!=', '1')->orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Gameslist::where('d', '!=', '1')->select('count(*) as allcount')->count();
        } else {
            $records = Gameslist::where('d', '!=', '1')->orderBy($columnName, $columnSortOrder)
                ->where('name', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Gameslist::where('d', '!=', '1')->select('count(*) as allcount')->where('name', 'like', '%'.$searchValue.'%')->count();
        }


        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                '_id' => $record->_id,
                'name' => $record->name,
                'image' => $record->image,
                'provider' => $record->provider,
                'category' => $record->category,
                'isDisabled' => $record->d === 1 ? true : false,
            ];
        }


        $response = [
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData' => $data_arr,
        ];

        return APIResponse::success($response);
    }

    public function game(Request $request)
    {
        $game = Gameslist::where('_id', $request->id)->first();

        return APIResponse::success([
            'game' => $game->toArray(),
            'ggr' => Providers::where('provider', $game->provider)->first()->ggr,
            'count' => GameResult::where('game', $game->id)->count(),
        ]);
    }

    public function providers(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length'); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value'] ?? null; // Search value

        // Total records
        $totalRecords = Providers::select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = Providers::orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Providers::select('count(*) as allcount')->count();
        } else {
            $records = Providers::orderBy($columnName, $columnSortOrder)
                ->where('provider', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Providers::select('count(*) as allcount')->where('provider', 'like', '%'.$searchValue.'%')->count();
        }

        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                'provider' => $record->provider,
                'image' => $record->img,
                'ggr' => $record->ggr,
                'games' => $record->games,
            ];
        }

        $response = [
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData' => $data_arr,
        ];

        return APIResponse::success($response);
    }

    public function updateProviders()
    {
        $apikey = config('settings.api_key');
        $response = Http::get('https://api.dk.games/v2/listProviders?apikey='.$apikey);
        $arrayList = ['data' => $response->json()];
        Providers::truncate();
        foreach ($arrayList['data'] as $providing) {
            Providers::where('provider', $providing['provider'])->update(['img' => $providing['img'], 'ggr' => $providing['ggr'], 'games' => $providing['games']], ['upsert' => true]);
        }
        \Artisan::call('optimize:clear');

        return APIResponse::success();
    }

    public function updateGames()
    {
        $apikey = config('settings.api_key');
        $response = Http::get('https://api.dk.games/v2/listGames?apikey='.$apikey.'&framework=1');
        $arrayList = ['data' => $response->json()];
        Gameslist::truncate();
        foreach ($arrayList['data'] as $game) {
            Gameslist::insert(['id' => $game['id'], 'name' => $game['name'], 'desc' => $game['desc'], 'provider' => $game['provider'], 'd' => $game['d'], 'demo' => $game['demo'], 'rtp' => $game['rtp'], 'category' => $game['category'], 'image' => $game['image'], 'image_sq' => $game['image_sq']]);
        }
        \Artisan::call('optimize:clear');

        return APIResponse::success();
    }

    public function restoreGamesList()
    {
        $url = config('app.url').'/js/defaultListGames.json';
        $response = Http::get($url);
        $arrayList = ['data' => $response->json()];
        Gameslist::truncate();
        foreach ($arrayList['data'] as $game) {
            Gameslist::insert(['id' => $game['id'], 'name' => $game['name'], 'desc' => $game['desc'], 'provider' => $game['provider'], 'd' => $game['d'], 'category' => $game['category'], 'image' => $game['image']]);
        }

        return APIResponse::success();
    }
}
