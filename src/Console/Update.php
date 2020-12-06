<?php

namespace Torann\Currency\Console;

use DateTime;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update
                                {--e|exchangeratesapi : Get rates from ExchangeRatesApi.io}
                                {--o|openexchangerates : Get rates from OpenExchangeRates.org}
                                {--g|google : Get rates from Google Finance}';

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
     * @throws \Exception
     */
    public function fire()
    {
        $this->handle();
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        // Get Settings
        $defaultCurrency = $this->currency->config('default');

        if ($this->input->getOption('exchangeratesapi')) {
            // Get rates from exchangeratesapi
            return $this->updateFromExchangeRatesApi($defaultCurrency);
        }

        if ($this->input->getOption('google')) {
            // Get rates from google
            return $this->updateFromGoogle($defaultCurrency);
        }

        if ($this->input->getOption('openexchangerates')) {
            if (! $api = $this->currency->config('api_key')) {
                $this->error('An API key is needed from OpenExchangeRates.org to continue.');

                return;
            }

            // Get rates from OpenExchangeRates
            return $this->updateFromOpenExchangeRates($defaultCurrency, $api);
        }
    }

    /**
     * Fetch rates from the API
     *
     * @param $defaultCurrency
     */
    private function updateFromExchangeRatesApi($defaultCurrency)
    {
        $this->info('Updating currency exchange rates from ExchangeRatesApi.io...');

        // Make request
        $content = json_decode($this->request("https://api.exchangeratesapi.io/latest?base={$defaultCurrency}"));

        // Error getting content?
        if (isset($content->error)) {
            $this->error($content->description);

            return;
        }

        // Update each rate
        foreach ($content->rates as $code => $value) {
            $this->currency->getDriver()->update($code, [
                'exchange_rate' => $value,
            ]);
        }

        $this->currency->clearCache();

        $this->info('Updated !');
    }

    /**
     * Fetch rates from the API
     *
     * @param $defaultCurrency
     * @param $api
     *
     * @throws \Exception
     */
    private function updateFromOpenExchangeRates($defaultCurrency, $api)
    {
        $this->info('Updating currency exchange rates from OpenExchangeRates.org...');

        // Make request
        $content = json_decode(
            $this->request("http://openexchangerates.org/api/latest.json?base={$defaultCurrency}&app_id={$api}&show_alternative=1")
        );

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
     * Fetch rates from Google Finance
     *
     * @param $defaultCurrency
     */
    private function updateFromGoogle($defaultCurrency)
    {
        $this->info('Updating currency exchange rates from finance.google.com...');

        foreach ($this->currency->getDriver()->all() as $code => $value) {
            // Don't update the default currency, the value is always 1
            if ($code === $defaultCurrency) {
                continue;
            }

            $response = $this->request('http://finance.google.com/finance/converter?a=1&from=' . $defaultCurrency . '&to=' . $code);

            if (Str::contains($response, 'bld>')) {
                $data = explode('bld>', $response);
                $rate = explode($code, $data[1])[0];

                $this->currency->getDriver()->update($code, [
                    'exchange_rate' => $rate,
                ]);
            } else {
                $this->warn('Can\'t update rate for ' . $code);
                continue;
            }
        }
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
