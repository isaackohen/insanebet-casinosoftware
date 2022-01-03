<?php

namespace App\Console\Commands;

use App\BonusBattles;
use App\Currency\Currency;
use App\Settings;
use App\User;
use Carbon\Carbon;
use App\Transaction;
use App\Events\BonusBattleUpdate;
use Illuminate\Console\Command;
use App\Events\UserNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomNotification;


class BonusBuyState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:bonusbuystate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating state of BonusBuy Battles';

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
        foreach (BonusBattles::where('room_state', '!=', 'completed')->where('room_state', '!=', 'cancelled')->where('players_counter', '=', 0)->get() as $room) {
 


               if($room->players_counter === 0) {
                $room->update(['room_state' => 'completed']);

                $win1 = $room->player_balance_1 ?? 0;
                $win2 = $room->player_balance_2 ?? 0;
                $win3 = $room->player_balance_3 ?? 0;
                $win4 = $room->player_balance_4 ?? 0;
                $arr = array('1' => $win1, '2' => $win2, '3' => $win3, '4' => $win4);
                $maxVal = max($arr);
                $maxKey = array_search($maxVal, $arr);
                $summedUp = $win1 + $win2 + $win3 + $win4;
                $winnerUserIdVar = 'player_id_'.$maxKey;
                $winnerNameVar = 'player_name_'.$maxKey;
                $winnerName = $room->$winnerNameVar;
                $winnerUserId = $room->$winnerUserIdVar;


                if($summedUp > 0) {

                $room->update(['winner_id' => $winnerUserId]);
                $room->update(['winner_amount' => $summedUp]);
                $room->update(['winner_name' => $winnerName]);

                    foreach($room->players as $player) {
                        if($player !== $winnerUserId) {
                            $userSearch = User::where('_id', $player)->first();
                            event(new UserNotification($userSearch, 'Bonus Battle Update - ID'.$room->room_id, 'You have lost bonus battle. Battle has been won by '.$winnerName.' with for total of '.$summedUp.'$.'));
                        } else {
                            $userSearch = User::where('_id', $player)->first();
                            event(new UserNotification($userSearch, 'Bonus Battle Update - ID'.$room->room_id, 'You have won the bonus battle for total of '.$summedUp.'$. Go to the Bonus Battle Lobby to claim your winnings.'));
                        }
                    }
}
 

}
        }
        foreach (BonusBattles::where('room_state', '!=', 'completed')->where('room_state', '!=', 'cancelled')->get() as $room) {
			if((Carbon::now() >= $room->expires) && ($room->room_state === 'joinable')) {
				//if room join expired
				foreach($room->players as $key=>$player) {
					$playerFinalVar = 'player_final_'.$key+1;
					$playerBalanceVar = 'player_balance_'.$key+1;
					$user = User::where('_id', $player)->first();
					$currency = Currency::find($room->currency);
					$amount = $currency->convertUSDToToken(($room->stake / 100) * 98);
					$room->update([
						$playerFinalVar => 'cancelled',
						$playerBalanceVar => 0.00,
					]);
                $notificationMessage = 'You were not in time for playing bonus battle. We have taken a 2% penalty of your buy-in and returned buy-in to your balance, please make sure to read our F.A.Q. section regarding bonus battles.';
                Notification::send($user, new CustomNotification('Bonus Battle Cancelled', $notificationMessage));

                $user->balance($currency)->add($amount, Transaction::builder()->message('Cancelled Bonus Battle')->get());
					//some event about cancelled lobby (private channel)
				}
				$room->update([
					'room_state' => 'cancelled'
				]);
				//some event about cancelled lobby (public channel)
				event(new BonusBattleUpdate($room));
			} else if ((Carbon::now() >= $room->expires) && ($room->room_state === 'started')) {
				//if room started expired
				foreach($room->players as $key=>$player) {
					$playerFinalVar = 'player_final_'.$key+1;
					$playerBalanceVar = 'player_balance_'.$key+1;
					if(($room->$playerFinalVar !== 'started') && ($room->$playerFinalVar !== 'finished')) {
						$room->update([
							$playerFinalVar => 'cancelled',
							$playerBalanceVar => 0.00,
							'players_counter' => $room->players_counter - 1
						]); 
					}
					//some event about cancelled player (private channel)
				}
				$room->update([
					'expires' => Carbon::now()->addMinutes(15),
					'room_state' => 'active'
				]);
				//some event about active lobby (public channel)
				event(new BonusBattleUpdate($room));
			} else if ((Carbon::now() >= $room->expires) && ($room->room_state === 'active')) {
				//if room active expired
				$cancelled = 0;
				foreach($room->players as $key=>$player) {
					$playerFinalVar = 'player_final_'.$key+1;
					if($room->$playerFinalVar !== 'cancelled') {
						$room->update([
							$playerFinalVar => 'finished'
						]); 
					}
					if($room->$playerFinalVar === 'cancelled') $cancelled++;
					//some event about player finished game (private channel)
				}
				if($cancelled === $room->players_active) {
					$room->update([
						'room_state' => 'cancelled'
					]);
				} else {
					$room->update([
						'room_state' => 'completed'
					]);
				}
				//some event about complete game (public channel)
				event(new BonusBattleUpdate($room));
			}
        }
    }
}
