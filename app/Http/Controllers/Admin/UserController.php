<?php

namespace App\Http\Controllers\Admin;

use App\Currency\Currency;
use App\Game;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Statistics;
use App\Transaction;
use App\TransactionStatistics;
use App\User;
use App\Utils\APIResponse;
use App\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\Decimal128;

class UserController extends Controller
{
    public function get(Request $request)
    {
        $user = User::where('_id', $request->id)->first();
        $statistics = Statistics::where('user', $request->id)->first();
        $currencies = [];
        $wins = 0;
        $losses = 0;
        foreach (Currency::all() as $currency) {
            $var1 = 'bets_'.$currency->id();
            $var2 = 'wins_'.$currency->id();
            $var3 = 'loss_'.$currency->id();
            $var4 = 'wagered_'.$currency->id();
            $wins += $statistics->data[$var2] ?? 0;
            $losses += $statistics->data[$var3] ?? 0;
            $currencies = array_merge($currencies, [
                $currency->id() => [
                    'games' => $statistics->data[$var1] ?? 0,
                    'wins' => $statistics->data[$var2] ?? 0,
                    'losses' => $statistics->data[$var3] ?? 0,
                    'wagered' => $statistics->data[$var4] ?? 0,
                    'deposited' => Invoice::where('user', $user->_id)->where('currency', $currency->id())->where('status', 1)->sum('sum'),
                    'balance' => $user->balance($currency)->get(),
                ],
            ]);
        }

        return APIResponse::success([
            'user' => $user->makeVisible($user->hidden)->toArray(),
            'games' => $statistics->data['games_played'] ?? 0,
            'wins' => $wins,
            'losses' => $losses,
            'freespins' => $user->makeVisible($user->hidden)->freespins ?? 0,
            'currencies' => $currencies, ]);
    }

    public function transactions(Request $request, $id)
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
        $totalRecords = Transaction::where('user', $id)->where('demo', '!=', true)->select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = Transaction::where('user', $id)->where('demo', '!=', true)->orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get()->reverse();
            $totalRecordswithFilter = Transaction::where('user', $id)->where('demo', '!=', true)->select('count(*) as allcount')->count();
        } else {
            $records = Transaction::where('user', $id)->where('demo', '!=', true)->orderBy($columnName, $columnSortOrder)
                ->where('name', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Transaction::where('user', $id)->where('demo', '!=', true)->select('count(*) as allcount')->where('name', 'like', '%'.$searchValue.'%')->count();
        }

        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                '_id' => $record->_id,
                'created_at' => $record->created_at,
                'data' => $record->data,
                'amount' => $record->amount,
                'currency' => $record->currency,
                'new' => $record->new,
                'old' => $record->old,
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

    public function games(Request $request, $id)
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
        $totalRecords = Game::where('demo', '!=', true)->where('user', $id)->select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = Game::where('demo', '!=', true)->where('user', $id)->orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get()->reverse();
            $totalRecordswithFilter = Game::where('demo', '!=', true)->where('user', $id)->select('count(*) as allcount')->count();
        } else {
            $records = Game::where('demo', '!=', true)->where('user', $id)->orderBy($columnName, $columnSortOrder)
                ->where('game', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Game::where('demo', '!=', true)->where('user', $id)->select('count(*) as allcount')->where('game', 'like', '%'.$searchValue.'%')->count();
        }

        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                '_id' => $record->_id,
                'game' => $record->game,
                'created_at' => $record->created_at,
                'wager' => $record->wager,
                'profit' => $record->profit,
                'currency' => $record->currency,
                'status' => $record->status,
                'data' => $record->data,
                'user' => $record->user,
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
	
	public function deposits(Request $request, $id)
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
        $totalRecords = Invoice::where('status', 1)->where('user', $id)->select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = Invoice::where('status', 1)->where('user', $id)->orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Invoice::where('status', 1)->where('user', $id)->select('count(*) as allcount')->count();
        } else {
            $records = Invoice::where('status', 1)->where('user', $id)->orderBy($columnName, $columnSortOrder)
                ->where('ledger', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Invoice::where('status', 1)->where('user', $id)->select('count(*) as allcount')->where('ledger', 'like', '%'.$searchValue.'%')->count();
        }

        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                '_id' => $record->_id,
                'updated_at' => $record->updated_at,
                'amount' => $record->amount,
                'currency' => $record->currency,
				'ledger' => $record->ledger,
                'sum' => $record->sum,
                'usd' => $record->usd,
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

    public function withdrawals(Request $request, $id)
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
        $totalRecords = Withdraw::where('status', 1)->orWhere('status', 2)->where('user', $id)->select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = Withdraw::where('status', 1)->orWhere('status', 2)->where('user', $id)->orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Withdraw::where('status', 1)->orWhere('status', 2)->where('user', $id)->select('count(*) as allcount')->count();
        } else {
            $records = Withdraw::where('status', 1)->orWhere('status', 2)->where('user', $id)->orderBy($columnName, $columnSortOrder)
                ->where('address', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Withdraw::where('status', 1)->orWhere('status', 2)->where('user', $id)->select('count(*) as allcount')->where('address', 'like', '%'.$searchValue.'%')->count();
        }

        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                '_id' => $record->_id,
                'updated_at' => $record->updated_at,
                'sum' => $record->sum,
                'currency' => $record->currency,
                'usd' => $record->usd,
                'withdraw_method' => $record->withdraw_method,
                'address' => $record->address,
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

    public function userTxStats(Request $request)
    {
        $user = User::where('_id', $request->id)->first();
        $TransactionStats = TransactionStatistics::statsGet($user->_id);
        $TransactionStats = $TransactionStats[0];

        return APIResponse::success([
            'promocode' => $TransactionStats['promocode'] ?? 0,
            'weeklybonus' => $TransactionStats['weeklybonus'] ?? 0,
            'freespins_amount' => $TransactionStats['freespins_amount' ?? 0],
            'faucet' => $TransactionStats['faucet'] ?? 0,
            'challenges' => $TransactionStats['challenges'] ?? 0,
            'depositbonus' => $TransactionStats['depositbonus'] ?? 0,
            'deposit_total' => $TransactionStats['deposit_total'] ?? 0,
            'deposit_count' => $TransactionStats['deposit_count'] ?? 0,
            'withdraw_count' => $TransactionStats['withdraw_count'] ?? 0,
            'withdraw_total' => $TransactionStats['withdraw_total'] ?? 0,
            'vip_progress' => $TransactionStats['vip_progress'] ?? 0,
            'tip_received' => $TransactionStats['tip_received'] ?? 0,
            'tip_sent' => $TransactionStats['tip_sent'] ?? 0,
        ]);
    }

    public function users(Request $request)
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
        $totalRecords = User::where('bot', '!=', true)->select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = User::where('bot', '!=', true)->orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = User::where('bot', '!=', true)->select('count(*) as allcount')->count();
        } else {
            $records = User::where('bot', '!=', true)->orderBy($columnName, $columnSortOrder)
                ->where('name', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = User::where('bot', '!=', true)->select('count(*) as allcount')->where('name', 'like', '%'.$searchValue.'%')->count();
        }

        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                '_id' => $record->_id,
                'avatar' => $record->avatar,
                'name' => $record->name,
                'created_at' => $record->created_at,
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

    public function checkDuplicates(Request $request)
    {
        $user = User::where('_id', $request->id)->first();
        if ($user->bot) {
            return APIResponse::reject(1, 'Can\'t verify bots');
        }

        return APIResponse::success([
            'user' => $user->makeVisible('register_multiaccount_hash')->makeVisible('login_multiaccount_hash')->toArray(),
            'same_register_hash' => User::where('register_multiaccount_hash', $user->register_multiaccount_hash)->get()->toArray(),
            'same_login_hash' => User::where('login_multiaccount_hash', $user->login_multiaccount_hash)->get()->toArray(),
            'same_register_ip' => User::where('register_ip', $user->register_ip)->get()->toArray(),
            'same_login_ip' => User::where('login_ip', $user->login_ip)->get()->toArray(),
        ]);
    }

    public function ban(Request $request)
    {
        $user = User::where('_id', request('id'))->first();
        (new \App\ActivityLog\BanUnbanLog())->insert(['type' => $user->ban ? 'unban' : 'ban', 'id' => $user->_id]);
        $user->update([
            'ban' => $user->ban ? false : true,
        ]);

        return APIResponse::success();
    }

    public function role(Request $request)
    {
        User::where('_id', request('id'))->update([
            'access' => request('role'),
        ]);

        return APIResponse::success();
    }

    public function balance(Request $request)
    {
        User::where('_id', request('id'))->update([
            request('currency') => new Decimal128(strval(request('balance'))),
        ]);

        (new \App\ActivityLog\BalanceChangeActivity())->insert(['currency' => request('currency'), 'balance' => request('balance'), 'id' => request('id')]);

        return APIResponse::success();
    }
}
