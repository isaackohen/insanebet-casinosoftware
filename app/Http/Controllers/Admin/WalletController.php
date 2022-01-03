<?php

namespace App\Http\Controllers\Admin;

use App\Currency\Currency;
use App\Http\Controllers\Controller;
use App\Invoice;
use App\Notifications\WithdrawAccepted;
use App\Notifications\WithdrawDeclined;
use App\User;
use App\Utils\APIResponse;
use App\Withdraw;
use App\TransactionStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use MongoDB\BSON\Decimal128;

class WalletController extends Controller
{
    public function ethereumNativeSendDeposits(Request $request)
    {
        foreach (Invoice::where('currency', 'native_eth')->where('redirected', '!=', true)->get() as $invoice) {
            Currency::find('native_eth')->send(User::where('_id', $invoice->user)->first()->wallet_native_eth, $request->toAddr, floatval((new Decimal128($invoice->sum))->jsonSerialize()['$numberDecimal']));
            $invoice->update(['redirected' => true]);
        }

        return APIResponse::success();
    }

    public function info()
    {
        $withdraws = [];
        $invoices = [];

        foreach (Withdraw::where('status', 0)->get() as $withdraw) {
            $user = User::where('_id', $withdraw->user)->first();
            if (! $user) {
                continue;
            }
            array_push($withdraws, [
                'withdraw' => $withdraw->toArray(),
                'user' => array_merge($user->toArray(), [
                    'vipLevel' => $user->vipLevel(),
                    'balance' => $user->balance(Currency::find($withdraw->currency))->get(),
                ]),
            ]);
        }

        return APIResponse::success([
            'withdraws' => $withdraws,
        ]);
    }

    public function deposits(Request $request)
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
        $totalRecords = Invoice::where('status', 1)->select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = Invoice::where('status', 1)->orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Invoice::where('status', 1)->select('count(*) as allcount')->count();
        } else {
            $records = Invoice::where('status', 1)->orderBy($columnName, $columnSortOrder)
                ->where('sum', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Invoice::where('status', 1)->select('count(*) as allcount')->where('sum', 'like', '%'.$searchValue.'%')->count();
        }

        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                '_id' => $record->_id,
                'updated_at' => $record->updated_at,
                'user' => $record->user,
                'username' => User::where('_id', $record->user)->first()->name,
                'amount' => $record->amount,
                'currency' => $record->currency,
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

    public function withdrawals(Request $request)
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
        $totalRecords = Withdraw::where('status', 1)->orWhere('status', 2)->select('count(*) as allcount')->count();

        // Fetch records
        if (! $searchValue) {
            $records = Withdraw::where('status', 1)->orWhere('status', 2)->orderBy($columnName, $columnSortOrder)
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Withdraw::where('status', 1)->orWhere('status', 2)->select('count(*) as allcount')->count();
        } else {
            $records = Withdraw::where('status', 1)->orWhere('status', 2)->orderBy($columnName, $columnSortOrder)
                ->where('address', 'like', '%'.$searchValue.'%')
                ->skip(intval($start))
                ->take(intval($rowperpage))
                ->get();
            $totalRecordswithFilter = Withdraw::where('status', 1)->orWhere('status', 2)->select('count(*) as allcount')->where('address', 'like', '%'.$searchValue.'%')->count();
        }

        $data_arr = [];
        $sno = $start + 1;
        foreach ($records as $record) {
            $data_arr[] = [
                '_id' => $record->_id,
                'updated_at' => $record->updated_at,
                'user' => $record->user,
                'username' => User::where('_id', $record->user)->first()->name,
                'sum' => $record->sum,
                'currency' => $record->currency,
                'usd' => $record->usd,
                'withdraw_method' => $record->withdraw_method,
                'address' => $record->address,
                'bankaccount_name' => $record->bankaccount_name,
                'bankaccount_ifsc' => $record->bankaccount_ifsc,
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

    public function infoIgnored()
    {
        $withdraws = [];

        foreach (Withdraw::where('status', 3)->get() as $withdraw) {
            $user = User::where('_id', $withdraw->user)->first();
            array_push($withdraws, [
                'withdraw' => $withdraw->toArray(),
                'user' => array_merge($user->toArray(), [
                    'vipLevel' => $user->vipLevel(),
                    'balance' => $user->balance(Currency::find($withdraw->currency))->get(),
                ]),
            ]);
        }

        return APIResponse::success([
            'withdraws' => $withdraws,
        ]);
    }

    public function accept(Request $request)
    {
        $withdraw = Withdraw::where('_id', request('id'))->first();
        if ($withdraw == null || $withdraw->status != 0) {
            return APIResponse::reject(1, 'Invalid state');
        }

        User::where('_id', $withdraw->user)->first()->notify(new WithdrawAccepted($withdraw));
        $withdraw->update([
            'status' => 1,
        ]);

        $statsGet = TransactionStatistics::statsGet($withdraw->user);
        TransactionStatistics::statsUpdate($withdraw->user, 'withdraw_total', (($statsGet->withdraw_total ?? 0) + $withdraw->usd));
        TransactionStatistics::statsUpdate($withdraw->user, 'withdraw_count', (($statsGet->withdraw_count ?? 0) + 1));

        return APIResponse::success();
    }

    public function decline(Request $request)
    {
        $withdraw = Withdraw::where('_id', request('id'))->first();
        if ($withdraw == null || $withdraw->status != 0) {
            return APIResponse::reject(1, 'Invalid state');
        }

        $withdraw->update([
            'decline_reason' => request('reason'),
            'status' => 2,
        ]);
        User::where('_id', $withdraw->user)->first()->notify(new WithdrawDeclined($withdraw));
        User::where('_id', $withdraw->user)->first()->balance(Currency::find($withdraw->currency))->add($withdraw->sum);

        return APIResponse::success();
    }

    public function ignore(Request $request)
    {
        $withdraw = Withdraw::where('_id', request('id'))->first();
        if ($withdraw == null || $withdraw->status != 0) {
            return APIResponse::reject(1, 'Invalid state');
        }
        $withdraw->update([
            'status' => 3,
        ]);

        return APIResponse::success();
    }

    public function unignore(Request $request)
    {
        $withdraw = Withdraw::where('_id', request('id'))->first();
        if ($withdraw == null || $withdraw->status != 3) {
            return APIResponse::reject(1, 'Invalid state');
        }
        $withdraw->update([
            'status' => 0,
        ]);

        return APIResponse::success();
    }

    public function autoSetup()
    {
        foreach (Currency::all() as $currency) {
            $currency->setupWallet();
        }

        return APIResponse::success();
    }

    public function transfer()
    {
        try {
            $currency = Currency::find(request('currency'));
            $currency->send($currency->option('transfer_address'), request('address'), floatval(request('amount')));
        } catch (\Exception $e) {
            Log::critical($e);

            return APIResponse::reject(1);
        }

        return APIResponse::success();
    }
}
