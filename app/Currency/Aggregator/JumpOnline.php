<?php

namespace App\Currency\Aggregator;

use App\Currency\Currency;
use App\Invoice;
use App\Transaction;
use App\TransactionStatistics;
use App\Events\Deposit;
use App\Settings;
use App\Events\UserNotification;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class JumpOnline extends Aggregator
{
    private function paymentId(Invoice $invoice)
    {
        return null;
    }

    public function invoice(Invoice $invoice): array
    {
        $currency = Currency::find('local_inr');
		$amount = number_format((float) $invoice->sum, 0, '.', '');
        $securedText = hash('sha256', config('settings.JTALK_CLIENTID').':123:'.config('settings.JTALK_AUTHKEY').':'.$amount);
        $user = User::where('_id', $invoice->user)->first();
        $invoice->update([
            'hash' => $securedText,
        ]);
        return [
			'url' => 'https://jumptalk.online/jtpayment/request.php',
			'data' => [
				'clientId' => config('settings.JTALK_CLIENTID'),
				'merchantCode' => '123',
				'securedText' => $securedText,
				'redirectUrl' => env('APP_URL').'/api/payment',
				'orderId' => $invoice->_id,
				'amount' => $amount,
				'email' => $user->email,
				'phone' => '02228873863'
			]
        ];
    }

    public function validate(Request $request): bool
    {
        return $request->merchantCode != null;
    }

    public function status(Request $request): string
    {
		$invoice = Invoice::where('_id', $request->orderId)->first();
		if ($invoice == null) {
            return redirect('/');
        }
        if ($invoice->status != 0) {
            return redirect('/');
        }
		
		$response = Http::post('https://jumptalk.online/jtpayment/status.php', [
			'clientId' => config('settings.JTALK_CLIENTID'),
			'merchantCode' => $request->merchantCode,
			'securedText' => hash('sha256', config('settings.JTALK_CLIENTID').':123:'.config('settings.JTALK_AUTHKEY')),
			'orderId' => $request->orderId,
			'refNo' => $request->refNo
		]);
		
		$data = json_decode($response);
		$payload = [
			'clientId' => config('settings.JTALK_CLIENTID'),
			'merchantCode' => $request->merchantCode,
			'securedText' => $invoice->hash,
			'orderId' => $request->orderId,
			'refNo' => $request->refNo
		];
		Log::info($payload);
		if(!isset($data->error)) { 
			Log::info($response);
			$user = User::where('_id', $invoice->user)->first();
			if($data->status === 'C') {
				// transaction was initiated and is under progress
				$status = 'Processing';
			}
			if($data->status === 'S') {
				//  transaction was successful
				$sum = number_format($data->amount, 2, '.', '');
				$depositAmount = Currency::find('local_inr')->convertTokenToUSD(floatval($sum));
				$invoice->update([
					'sum' => $sum,
					'usd' => $depositAmount,
					'status' => 1,
				]);
				$status = 'Paid';
				if (($user->first_deposit_bonus !== null) && ($user->first_deposit_bonus === 'activated') && (Settings::get('first_deposit_status') === 'true')) {
					$doubler = $invoice->usd * 2;
					$user->balance(Currency::find('local_bonus'))->add($doubler, Transaction::builder()->message('Deposit credited (Doubler Bonus)')->get());
					$user->update([
						'first_deposit_bonus' => 'used',
						'bonus_goal' => ($doubler * floatval(Settings::get('first_deposit_rules'))),
					]);
					event(new Deposit($user, Currency::find('local_bonus'), $doubler));
				} else {
					$user->balance(Currency::find('local_inr'))->add($invoice->sum, Transaction::builder()->message('Deposit credited')->get());
					event(new Deposit($user, Currency::find('local_inr'), $invoice->sum));
				}
				$statsGet = TransactionStatistics::statsGet($user->_id);
				TransactionStatistics::statsUpdate($user->_id, 'deposit_total', (($statsGet->deposit_total ?? 0) + $depositAmount));
				TransactionStatistics::statsUpdate($user->_id, 'deposit_count', (($statsGet->deposit_count ?? 0) + 1));

				$telegramChannel = Settings::get('telegram_internal_channel');
				$messageAlert = 'Deposit credited on Casino '.config('app.name').' for '.$depositAmount.'$ from user '.$user->name.'.';
				$url = 'http://alerts.sh/api/alert/telegramMessage?message='.$messageAlert.'&button_text=Visit'.config('app.name').'&button_url='.config('app.url').'&channel='.$telegramChannel;
				try {
					$result = file_get_contents($url);
				} catch (\Exception $e) {
					Log:info($e);
				}
			}
			if($data->status === 'F') {
				// transaction was failed
				$invoice->update([
					'status' => 2,
				]);
				$status = 'Failed';
			}
			event(new UserNotification($user, 'Payment Status', 'Your recent payment status has been updated to: '.$status));
		} else {
			Log::info($response);
		}
		
        return redirect('/');
    }

    public function id(): string
    {
        return 'jumponline';
    }

    public function name(): string
    {
        return 'JumpOnline';
    }

    public function icon(): string
    {
        return asset('/img/payment/freekassa.svg');
    }
}
