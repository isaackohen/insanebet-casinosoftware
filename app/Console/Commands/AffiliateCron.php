<?php

namespace App\Console\Commands;

use App\Currency\Currency;
use App\Currency\Option\WalletOption;
use App\Events\Deposit;
use App\Http\Controllers\Api\WalletController;
use App\Invoice;
use App\Settings;
use App\Transaction;
use App\Statistics;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AffiliateCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:affiliatecron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Affiliate Claimable Bonus Update';

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
        foreach (Statistics::where('affiliate_rakeback' > 0)->get() as $rakeback) {
            $amountRakeback = $rakeback->affiliate_rakeback;

            $userAffiliate = User::where('_id', $rakeback->user)->first();
            $claimable = User::where('_id', $userAffiliate->referral)->first()->referral_claimable ?? 0;
            User::where('_id', $userAffiliate->referral)->update([
            'referral_claimable' => $claimable + $rakeback->affiliate_rakeback
            ]);

            $rakeback->update([
            'affiliate_rakeback' => 0,
            'affiliate_total' => $rakeback->affiliate_total + $rakeback->affiliate_rakeback,
            ]);
        }
    }
}
