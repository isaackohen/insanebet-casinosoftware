<?php

namespace App\Console\Commands;

use App\Currency\Currency;
use App\User;
use Illuminate\Console\Command;
use App\Settings;

class WalletReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:walletreset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset assigned wallet addresses';

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
        foreach (Currency::all() as $currency) {
            $wallet = 'wallet_'.$currency->id();
            User::query()->update([
                $wallet => null,
            ]);
        }
        $val = 'withdraw_count_daily';
        Settings::get($val, 0, true);
        Settings::where('name', $val)->update(['value' => 0]);

        $clear = \Artisan::call('optimize:clear');
        $view = \Artisan::call('view:cache');
        $route = \Artisan::call('route:cache');
    }
}
