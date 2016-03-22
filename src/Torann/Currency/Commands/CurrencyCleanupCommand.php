<?php

namespace Torann\Currency\Commands;

use Torann\Currency\Currency;
use Illuminate\Console\Command;

class CurrencyCleanupCommand extends Command
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
     *
     * @param Currency $currency
     */
    public function __construct(Currency $currency)
    {
        $this->currency = $currency;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->currency->clearCache();

        $this->info('Currency cache cleaned.');
    }
}