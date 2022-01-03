<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Currency\Currency;
use App\User;
use Carbon\Carbon;
use App\Events\Deposit;
use App\Settings;
use App\Transaction;
use App\TransactionStatistics;
use Illuminate\Console\Command;
use App\Events\UserNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;


class JumpOnlineStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:jumpOnlineStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating JumpOnline payment status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		
		foreach (Invoice::where('aggregator', 'jumponline')->where('created_at', '>=', Carbon::now()->subDays(3))->where('status', 0)->get() as $invoice) {
			$response = Http::post('https://jumptalk.online/jtpayment/statusbyorderid.php', [
				'clientId' => env('JTALK_CLIENTID'),
				'securedText' => hash('sha256', env('JTALK_CLIENTID').':'.env('JTALK_AUTHKEY').':'.$invoice->_id),
				'orderId' => $invoice->_id
			]);
			
			$data = json_decode($response);
			if(!isset($data->error)) {
				Log::info($response);
				$user = User::where('_id', $invoice->user)->first();
				if($data->status === 'C') {
					// transaction was initiated and is under progress
					$status = 'Processing';
				}
				if($data->status === 'S') {
					//  transaction was successful
					$depositAmount = Currency::find('local_inr')->convertTokenToUSD(floatval($data->amount));
					$invoice->update([
						'sum' => floatval($data->amount),
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
					$result = file_get_contents($url);
				}
				if($data->status === 'F') {
					// transaction was failed
					$invoice->update([
						'status' => 2,
					]);
					$status = 'Failed';
				}
				event(new UserNotification($user, 'Payment Status', 'Your recent payment status has been updated to: '.$status));
			}
		}
		
    }
	
}
