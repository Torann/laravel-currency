<?php

namespace Torann\Currency\Console;

use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update
                                {--o|openexchangerates : Get rates from OpenExchangeRates.org}
                                {--f|fixer : Get rates from fixer.io}';

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

        if ($this->input->getOption('fixer')) {
            $api = $this->currency->config('api_key');
            if (!$api = $this->currency->config('api_key')) {
                $this->error('An API key is needed from fixer.io to continue.');
                return;
            }
            // Get rates from fixer
            $this->updateFromFixer($defaultCurrency, $api);
            return;
        }

        if ($this->input->getOption('openexchangerates')) {
            if (!$api = $this->currency->config('api_key')) {
                $this->error('An API key is needed from OpenExchangeRates.org to continue.');

                return;
            }

            // Get rates from OpenExchangeRates
            $this->updateFromOpenExchangeRates($defaultCurrency, $api);
            return;
        }
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
     * Fetch rates from Fixer.io
     *
     * @param $defaultCurrency
     * @param $api
     */
    private function updateFromFixer($defaultCurrency, $api)
    {
        $this->info('Updating currency exchange rates from fixer.io...');

        // first thing we need to do is exchange our base currency into EUR which is the only one we get for free
        if ($defaultCurrency !== 'EUR') {
            $response = $this->request(
                'http://data.fixer.io/api/latest?access_key=' . $api
                . '&base='    . 'EUR'
                . '&symbols=' . $defaultCurrency
            );
            $baseRate = json_decode($response)->rates->$defaultCurrency;
        }

        $response = $this->request(
            'http://data.fixer.io/api/latest?access_key=' . config('currency.api_key')
            . '&base='    . 'EUR'
            . '&symbols=' . implode(',', array_keys($this->currency->getDriver()->all()))
        );

        $parsedResponse = json_decode($response);
        if ($parsedResponse === null) {
            $this->error('Received invalid response!');
            return;
        }
        if ($parsedResponse->success === false) {
            $this->error('Unable to get rates: ' . $parsedResponse->error->type);
            return;
        }

        foreach ($parsedResponse->rates as $code => $rate) {
            $this->info('Updating ' . $code . ' to ' . ($rate / $baseRate));
            $this->currency->getDriver()->update($code, [
                'exchange_rate' => $rate / $baseRate,
                'updated_at' => date('Y-m-d h:i:s', $parsedResponse->timestamp)
            ]);
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
