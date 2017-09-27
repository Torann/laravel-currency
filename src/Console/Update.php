<?php

namespace Torann\Currency\Console;

use Illuminate\Console\Command;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update
                                {--o|openexchangerates : Get rates from OpenExchangeRates.org}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update exchange rates from an online source';

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
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->input->getOption('openexchangerates')) {
            if (!$api = $this->currency->config('api_key')) {
                $this->error('An API key is needed from OpenExchangeRates.org to continue.');

                return;
            }

            // Get rates
            $this->comment('Updating currency exchange rates from OpenExchangeRates.org...');

            $this->currency->updateRates(true);
        }
        else {
            // Get rates
            $this->comment('Updating currency exchange rates from Finance Yahoo...');

            $this->currency->updateRates();
        }

        $this->info('Success!');
    }
}
