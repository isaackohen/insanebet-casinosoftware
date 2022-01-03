<?php

namespace App\Http\Controllers\Api;

use App\Currency\Aggregator\Aggregator;
use App\Currency\Currency;
use App\Events\Deposit;
use App\Invoice;
use App\Settings;
use App\Transaction;
use App\TransactionStatistics;
use App\User;
use App\Utils\APIResponse;
use App\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentsController
{
    public function walletNotify($currency, $txid)
    {
        Currency::find($currency)->process($txid);

        return APIResponse::success();
    }

    public function blockNotify($currency, $blockId)
    {
        Currency::find($currency)->processBlock($blockId);

        return APIResponse::success();
    }

    public function bitgoWebhook()
    {
        $sdk = Currency::find('bg_btc')->getSDK();
        $payload = $sdk->getWebhookPayload();

        $currency = Currency::find('bg_'.$payload['coin']);
        if ($currency === null) {
            Log::error('Invalid BitGo webhook currency: bg_'.$payload['coin']);

            return APIResponse::reject(1, 'Invalid request');
        }

        $result = $currency->process();

        return APIResponse::success(is_array($result) ? $result : []);
    }

    public function paymentStatus(Request $request)
    {
        $aggregator = null;
        foreach (Aggregator::list() as $ag) {
            if ($ag->validate($request)) {
                $aggregator = $ag;
                break;
            }
        }
        if ($aggregator == null) {
            return redirect('/');
        }

        return $aggregator->status($request);
    }

    public function depositNowpaymentsCallback(Request $request)
    {
        Log::emergency(json_encode($request->all()));


        if (! $request->has('order_description') || Invoice::where('hash', $request->get('order_description'))->count() === 0) {
            return response('Ok')
                ->header('Content-Type', 'text/plain');
        }

        if ($request->payment_status !== 'waiting') {
            $invoice = Invoice::where('hash', $request->get('order_description'))
                ->where('status', 0)
                ->first();

            $user = User::find($invoice->user);
            $eventUpdated = event(new \App\Events\UserNotification($user, 'Payment Status', 'Your recent payment status has been updated to: '.$request->payment_status));
        }
        if ($request->payment_status == 'confirming') {
            $invoice = Invoice::where('hash', $request->get('order_description'))
                ->where('status', 0)
                ->first();

            $user = User::find($invoice->user);
            $sum = $request->get('actually_paid');
            $currency = $invoice->currency;
            $user->update(['wallet_'.$currency => null]);

            return response('Ok')
            ->header('Content-Type', 'text/plain');
        }

        if ($request->payment_status == 'expired') {
            $invoice = Invoice::where('hash', $request->get('order_description'))
            ->where('status', 0)
            ->first();

            $user = User::find($invoice->user);
            $currency = $invoice->currency;
            $user->update(['wallet_'.$currency => null]);

            return response('Ok')
                ->header('Content-Type', 'text/plain');
        }

        if (! $request->payment_status == 'finished' || Invoice::where('hash', $request->get('order_description'))->where('status', 0)->count() === 0) {
            return response('Ok')
                ->header('Content-Type', 'text/plain');
        }

        if ($request->payment_status == 'finished') {
            $invoice = Invoice::where('hash', $request->get('order_description'))
            ->where('status', 0)
            ->first();

            $user = User::find($invoice->user);
            $currency = $invoice->currency;
            $depositAmount = Currency::find($currency)->convertTokenToUSD(floatval($request->get('actually_paid')));
            $invoice->update(['status' => 1, 'usd' => $depositAmount, 'sum' => $request->get('actually_paid')]);
            $currency = $invoice->currency;

            $user->update(['wallet_'.$currency => null]);
            if (($user->first_deposit_bonus !== null) && ($user->first_deposit_bonus === 'activated') && (Settings::get('first_deposit_status') === 'true')) {
                $doubler = Currency::find($currency)->convertTokenToUSD(floatval($request->get('actually_paid') * 2));
                $user->balance(Currency::find('local_bonus'))->add($doubler, Transaction::builder()->message('Deposit credited (Doubler Bonus)')->get());
                $user->update([
                    'first_deposit_bonus' => 'used',
                    'bonus_goal' => ($doubler * floatval(Settings::get('first_deposit_rules'))),
                ]);
                event(new Deposit($user, Currency::find('local_bonus'), $doubler));
            } else {
                $user->balance(Currency::find($currency))->add(floatval($request->get('actually_paid')), Transaction::builder()->message('Deposit credited')->get());
                event(new Deposit($user, Currency::find($currency), floatval($request->get('actually_paid'))));
            }
            $statsGet = TransactionStatistics::statsGet($user->_id);
            TransactionStatistics::statsUpdate($user->_id, 'deposit_total', (($statsGet->deposit_total ?? 0) + $depositAmount));
            TransactionStatistics::statsUpdate($user->_id, 'deposit_count', (($statsGet->deposit_count ?? 0) + 1));

            $telegramChannel = Settings::get('telegram_internal_channel');
            $messageAlert = 'Deposit credited on Casino '.config('app.name').' for '.$depositAmount.'$ from user '.$user->name.'.';
            $url = 'http://alerts.sh/api/alert/telegramMessage?message='.$messageAlert.'&button_text=Visit '.config('app.name').'&button_url='.config('app.url').'&channel='.$telegramChannel;
            $result = file_get_contents($url);

            return response('Ok')
            ->header('Content-Type', 'text/plain');
        }
    }

    public function depositChaingateway(Request $request)
    {
        Log::warning(json_encode($request->all()));
        if ($request->action == 'deposit') {
            $invoice = Invoice::where('ledger', $request->get('binancecoinaddress'))
                ->where('status', 0)
                ->first();

            $user = User::find($invoice->user);
            $invoice->update(['status' => 1, 'sum' => $request->get('amount')]);
            $currency = $invoice->currency;

            $user->update(['wallet_'.$currency => null]);

            if (($user->first_deposit_bonus !== null) && ($user->first_deposit_bonus === 'activated') && (Settings::get('first_deposit_status') === 'true')) {
                $doubler = Currency::find($currency)->convertTokenToUSD(floatval($request->get('amount') * 2));
                $user->balance(Currency::find('local_bonus'))->add($doubler, Transaction::builder()->message('Deposit credited (Doubler Bonus)')->get());
                $user->update([
                    'first_deposit_bonus' => 'used',
                    'bonus_goal' => ($doubler * floatval(Settings::get('first_deposit_rules'))),
                ]);
                event(new Deposit($user, Currency::find('local_bonus'), $doubler));
            } else {
                $user->balance(Currency::find($currency))->add(floatval($request->get('amount')), Transaction::builder()->message('Deposit credited')->get());
                $user->balance(Currency::find($currency))->add($request->get('amount'));
                event(new Deposit($user, Currency::find($currency), floatval($request->get('amount'))));
            }
            $depositAmount = Currency::find($currency)->convertTokenToUSD(floatval($request->get('amount')));
            $statsGet = TransactionStatistics::statsGet($user->_id);
            TransactionStatistics::statsUpdate($user->_id, 'deposit_total', ($statsGet->deposit_total + $depositAmount));
            TransactionStatistics::statsUpdate($user->_id, 'deposit_count', ($statsGet->deposit_count + 1));


            header('Content-Type: application/json');
            $response = ['ok' => true];
            $response = json_encode($response);

            return $response;
        }
    }

    public function withdrawalsNowpaymentsCallback(Request $request)
    {

       //Log::warning(json_encode($request->all()));
        if ($request->status !== 'FINISHED') {
            $withdraw = Withdraw::where('address', $request->get('address'))
            ->where('status', 0)
            ->first();
            if ($request->status === 'WAITING') {
                $request->status = 'QUEUED';
            }
            if ($request->status === 'EXPIRED' || $request->status === 'expired') {
                return 'done';
            }
            $withdraw->update(['withdraw_meta' => $request->status]);
            $user = \App\User::where('_id', $withdraw->user)->first();
            $eventUpdated = event(new \App\Events\UserNotification($user, 'Withdraw Status', 'Your withdraw request status has been updated to: '.$request->status));

            if ($request->status === 'FAILED') {
                $telegramChannel = Settings::get('telegram_internal_channel');
                $messageAlert = 'Automatic Withdrawal has failed (probably wrong wallet address and/or not enough hot wallet funds) on '.config('app.name').' for '.$withdraw->usd.'$ from user '.auth('sanctum')->user()->name.'.';
                $url = 'http://alerts.sh/api/alert/telegramMessage?message='.$messageAlert.'&button_text=Visit '.config('app.name').' ADMIN&button_url='.config('app.url').'/admin/&channel='.$telegramChannel;
                $result = file_get_contents($url);
            }

            return response('Ok')
                ->header('Content-Type', 'text/plain');
        }

        if ($request->status == 'FINISHED') {
            $withdraw = Withdraw::where('address', $request->get('address'))
            ->where('status', 0)
            ->first();

            $valDaily = 'withdraw_count_daily';
            $currentDaily = Settings::where('name', $valDaily)->first()->value;
            Settings::where('name', $valDaily)->update(['value' => ($withdraw->usd + $currentDaily)]);

            $val3hrs = 'withdraw_count_3hrs';
            $current3hrs = Settings::where('name', $val3hrs)->first()->value;
            Settings::where('name', $val3hrs)->update(['value' => ($withdraw->usd + $current3hrs)]);


            $withdraw->update(['status' => '1', 'withdraw_meta' => 'FINISHED']);
            $user = \App\User::where('_id', $withdraw->user)->first();
            $test = event(new \App\Events\UserNotification($user, 'Withdraw Completed', 'Your withdraw has been sent to your wallet.'));

            $statsGet = TransactionStatistics::statsGet($user->_id);
            TransactionStatistics::statsUpdate($user->_id, 'withdraw_total', ($statsGet->withdraw_total + $withdraw->usd));
            TransactionStatistics::statsUpdate($user->_id, 'withdraw_count', ($statsGet->withdraw_count + 1));

            $telegramChannel = Settings::get('telegram_internal_channel');
            $messageAlert = 'Withdraw completed on '.config('app.name').' for '.$withdraw->usd.'$ from user '.$user->name.'.';
            $url = 'http://alerts.sh/api/alert/telegramMessage?message='.$messageAlert.'&button_text=Visit '.config('app.name').'&button_url='.config('app.url').'&channel='.$telegramChannel;
            $result = file_get_contents($url);

            return response('Ok')
                ->header('Content-Type', 'text/plain');
        }
    }
}
