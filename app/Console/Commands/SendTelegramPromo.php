<?php

namespace App\Console\Commands;

use App\Events\PublicUserNotification;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class SendTelegramPromo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:telegrampromocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send promocode to Telegram channel';

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
        $dollaramount = floatval(Settings::where('name', 'promo_dollar')->first()->value);
        $sum = number_format(($dollaramount / \App\Http\Controllers\Api\WalletController::rateDollarBnb()), 8, '.', '');
        $usages = intval(Settings::where('name', 'vip_promo_usages')->first()->value);

        $promocode = \App\Promocode::create([
            'code' => \App\Promocode::generate(),
            'used' => [],
            'sum' => $sum,
            'usages' => $usages,
            'currency' => 'bnb',
            'times_used' => 0,
            'expires' => \Carbon\Carbon::now()->addHours(1),
            'vip' => false,
        ]);

        event(new PublicUserNotification('Promocode', 'Promocode Drop on our Telegram! Make sure to join our Telegram.'));
        $alertmessage = 'Bounty Treasure! For '.$promocode->sum.' BTC: '.$promocode->code.' - '.$promocode->usages.' uses.';

        $telegramChannel = Settings::get('telegram_public_channel');
        $imageGame = 'https://bigz.imgix.net/i/tgthumb/dropz/treasurekeybounty-3.png';
        $url = 'http://alerts.sh/api/alert/telegramImage?image='.$imageGame.'&message='.$alertmessage.'&button_text=Visit Casino!&button_url='.config('app.url').'&channel='.$telegramChannel;
        $result = file_get_contents($url);
    }
}
