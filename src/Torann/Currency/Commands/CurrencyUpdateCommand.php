<?php

namespace Torann\Currency\Commands;

use DateTime;
use Torann\Currency\Currency;
use Illuminate\Console\Command;

class CurrencyUpdateCommand extends Command
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
        // Get Settings
        $defaultCurrency = $this->currency->getConfig('default');

        if ($this->input->getOption('openexchangerates')) {
            if (!$api = $this->currency->getConfig('api_key')) {
                $this->error('An API key is needed from OpenExchangeRates.org to continue.');

                return;
            }

            // Get rates
            $this->updateFromOpenExchangeRates($defaultCurrency, $api);
        }
        else {
            // Get rates
            $this->updateFromYahoo($defaultCurrency);
        }
    }

    private function updateFromYahoo($defaultCurrency)
    {
        $this->info('Updating currency exchange rates from Finance Yahoo...');

        $data = [];

        // Get all currencies
        foreach ($this->currency->getDriver()->all() as $code => $value) {
            $data[] = "{$defaultCurrency}{$code}=X";
        }

        // Ask Yahoo for exchange rate
        if ($data) {
            $content = $this->request('http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');

            $lines = explode("\n", trim($content));

            // Update each rate
            foreach ($lines as $line) {
                $code = substr($line, 4, 3);
                $value = substr($line, 11, 6) * 1.00;

                if ($value) {
                    $this->currency->getDriver()->update($code, $value);
                }
            }

            $this->currency->clearCache();
        }

        $this->info('Update!');
    }

    private function updateFromOpenExchangeRates($defaultCurrency, $api)
    {
        $this->info('Updating currency exchange rates from OpenExchangeRates.org...');

        // Make request
        $content = json_decode($this->request("http://openexchangerates.org/api/latest.json?base={$defaultCurrency}&app_id={$api}"));

        // Error getting content?
        if (isset($content->error)) {
            $this->error($content->description);
            return;
        }

        // Parse timestamp for DB
        $timestamp = new DateTime(strtotime($content->timestamp));

        // Update each rate
        foreach ($content->rates as $code => $value) {
            $this->currency->getDriver()->update($code, $value, $timestamp);
        }

        $this->currency->clearCache();

        $this->info('Update!');
    }

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