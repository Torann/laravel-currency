<?php

namespace Torann\Currency\Console;

use Illuminate\Console\Command;

class Cleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup currency cache';

    /**
     * Currency instance
     *
     * @var \Torann\Currency\Currency
     */
    protected $currency;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        $this->currency = app('currency');

        parent::__construct();
    }

    /**
     * Execute the console command for Laravel 5.4 and below
     *
     * @return void
     */
    public function fire()
    {
        $this->handle();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Clear cache
        $this->currency->clearCache();
        $this->comment('Currency cache cleaned.');

        // Force the system to rebuild cache
        $this->currency->getCurrencies();
        $this->comment('Currency cache rebuilt.');
    }
}
