<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class onBuildEnd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dk:onBuildEnd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return int
     */
    public function handle()
    {
        /*
           $view = \Artisan::call('view:cache');
            $route = \Artisan::call('route:cache');

        */
    }
}
