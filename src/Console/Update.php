<?php

namespace Torann\Currency\Console;

use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update {currency?}
                                {--o|openexchangerates : Get rates from OpenExchangeRates.org}
                                {--f|fixer : Get rates from Fixer}';

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
        // Get Settings
        $defaultCurrency = $this->currency->config('default');
        
        $source = config('currency.source');
        
        // Check options
	    if ($this->input->getOption('openexchangerates')) {
		    $source = "openexchangerates";
	    } else if ($this->input->getOption('fixer')) {
	    	$source = "fixer";
	    }
	    
        // Get rates
	    if ($source === 'openexchangerates') {
	    	
	    	// Make sure the API key is set
		    if (!$api = $this->currency->config('api_key')) {
			    $this->error('An API key is needed from OpenExchangeRates.org to continue.');
			    return;
		    }
		    
		    $this->updateFromOpenExchangeRates($defaultCurrency, $api);
		    
	    } else if ($source === 'fixer') {
		    $this->updateFromFixer($defaultCurrency);
	    } else {
	    	$this->error('There is no source configured to update the currencies from.');
	    }
	    
    }
    
    private function updateFromFixer($defaultCurrency)
    {
        $this->info('Updating currency exchange rates from Fixer.io...');
        
        $result = json_decode($this->request("http://api.fixer.io/latest?base={$defaultCurrency}"));
	    
	    if (isset($result->error)) {
		    $this->error($result->description);
		    return;
	    }
	    
	    // As there's no timestamp, according to Fixer.io, it updates at around 4pm CET.
	    // Hence why we're manually putting in the time here.
	    $timestamp = new DateTime($result->date . ' 16:00:00', new DateTimeZone('CET'));
	    
	    // Update each rate
	    foreach ($result->rates as $code => $value) {
		    $this->currency->getDriver()->update($code, [
			    'exchange_rate' => $value,
			    'updated_at' => $timestamp,
		    ]);
	    }
	
	    // Fixer doesn't return the base rate as 1, hence we need to update it manually.
	    $this->currency->getDriver()->update($defaultCurrency, [
		    'exchange_rate' => 1,
		    'updated_at' => $timestamp,
	    ]);
	    
    }

    /**
     * Fetch rates from the API
     *
     * @param $defaultCurrency
     * @param $api
     */
    private function updateFromOpenExchangeRates($defaultCurrency, $api)
    {
        $this->info('Updating currency exchange rates from OpenExchangeRates.org...');

        // Make request
        $content = json_decode($this->request("http://openexchangerates.org/api/latest.json?base={$defaultCurrency}&app_id={$api}&show_alternative=1"));

        // Error getting content?
        if (isset($content->error)) {
            $this->error($content->description);

            return;
        }

        // Parse timestamp for DB
        $timestamp = (new DateTime())->setTimestamp($content->timestamp);

        // Update each rate
        foreach ($content->rates as $code => $value) {
            $this->currency->getDriver()->update($code, [
                'exchange_rate' => $value,
                'updated_at' => $timestamp,
            ]);
        }

        $this->currency->clearCache();

        $this->info('Update!');
    }

    /**
     * Make the request to the sever.
     *
     * @param $url
     *
     * @return string
     */
    private function request($url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_MAXCONNECTS, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
