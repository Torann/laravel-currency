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
                                {--f|fixer : Get rates from Fixer.io}';

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
            if (!$api = $this->currency->config('exchangerates_api_key')) {
                $this->error('An API key is needed from Exchangeratesapi.io to continue.');

                return;
            }
            // Get rates from Exchangeratesapi
            return $this->updateFromExchangeRatesApi($defaultCurrency, $api);
        }

        if ($this->input->getOption('fixer')) {
            if (!$api = $this->currency->config('fixer_api_key')) {
                $this->error('An API key is needed from Fixer.io to continue.');

                return;
            }

            // Get rates from Fixer
            return $this->updateFromFixerIO($defaultCurrency, $api);
        }

        if ($this->input->getOption('openexchangerates')) {
            $apiLegacy = $this->currency->config('api_key');
            $api = $this->currency->config('openexchangerates_api_key');

            if (!$apiLegacy && !$api) { //to stay retro compatible
                $this->error('An API key is needed from OpenExchangeRates.org to continue.');

                return;
            } else if ($apiLegacy && !$api) { //to stay retro compatible
                $api = $apiLegacy;
                $this->warn('Configuration "api_key" is deprecated please use "openexchangerates_api_key" instead');
            }

            // Get rates from OpenExchangeRates
            return $this->updateFromOpenExchangeRates($defaultCurrency, $api);
        }

        $this->error('Parameter is missing, please refer to --help to have all the parameter possible');
    }

    /**
     * Fetch rates from the API
     *
     * @param $defaultCurrency
     * @param $api
     */
    private function updateFromExchangeRatesApi($defaultCurrency, $api)
    {
        $this->info('Updating currency exchange rates from ExchangeRatesApi.io...');

        // Make request
        $content = json_decode($this->request("http://api.exchangeratesapi.io/v1/latest?base={$defaultCurrency}&access_key={$api}"));

        // Error getting content?
        if (isset($content->error)) {
            $this->log_error($content);
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

        $url = "http://openexchangerates.org/api/latest.json?app_id={$api}&show_alternative=1";

        // Make request
        $content = json_decode(
            $this->request($url . "&base={$defaultCurrency}")
        );
        // logger()->info(json_encode($content));
        // Error getting content?
        if (isset($content->error)) {
            $strToCompare = 'Changing the API `base` currency is available';
            if (strncmp($content->description, $strToCompare, strlen($strToCompare)) === 0) {
                $this->warn($content->description);
                $this->warn("Trying to retrieve exchange rates from the default currency then convert to $defaultCurrency, small divergence may appear!");
                $content = json_decode(
                    $this->request($url)
                );
                if (isset($content->error)) {
                    $this->log_error($content);
                    return;
                }
                if (!isset($content->rates->{$defaultCurrency})) {
                    $this->error("Can't find the default currency '$defaultCurrency'");
                    return;
                }
                $rateCorrection = $content->rates->{$defaultCurrency};
                foreach ($content->rates as $code => $value) {
                    if ($code === $defaultCurrency) {
                        $content->rates->{$code} = 1;
                    } else {
                        $content->rates->{$code} = round($content->rates->{$code} / $rateCorrection, 6);
                    }
                }
                $content->base = $defaultCurrency;
            } else {
                $this->log_error($content);
                return;
            }
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
     * Fetch rates from Fixer API
     *
     * @param $defaultCurrency
     * @param $api
     */
    private function updateFromFixerIO($defaultCurrency, $api)
    {
        $this->info('Updating currency exchange rates from fixer.io...');
        $url = "http://data.fixer.io/api/latest?access_key={$api}&format=1";

        // Make request
        $content = json_decode($this->request($url . "&base={$defaultCurrency}"));

        // Error getting content?
        if (isset($content->error)) {
            $strToCompare = 'base_currency_access_restricted';
            if (isset($content->error->type) && strncmp($content->error->type, $strToCompare, strlen($strToCompare)) === 0) {
                $this->warn($content->error->type);
                $this->warn("Trying to retrieve exchange rates from the default currency then convert to $defaultCurrency, small divergence may appear!");
                $content = json_decode(
                    $this->request($url)
                );
                if (isset($content->error)) {
                    $this->log_error($content);
                    return;
                }
                if (!isset($content->rates->{$defaultCurrency})) {
                    $this->error("Can't find the default currency '$defaultCurrency'");
                    return;
                }
                $rateCorrection = $content->rates->{$defaultCurrency};
                foreach ($content->rates as $code => $value) {
                    if ($code === $defaultCurrency) {
                        $content->rates->{$code} = 1;
                    } else {
                        $content->rates->{$code} = round($content->rates->{$code} / $rateCorrection, 6);
                    }
                }
                $content->base = $defaultCurrency;
            } else {
                $this->log_error($content);
                return;
            }
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

    function log_error($content)
    {
        if (isset($content->description)) {
            $this->error($content->description);
        } elseif (isset($content->error->message)) {
            $this->error($content->error->message);
        } elseif (isset($content->description)) {
            $this->error($content->description);
        } elseif (isset($content->error->info)) {
            $this->error($content->error->info);
        } elseif (isset($content->error->type)) {
            $this->error($content->error->type);
        } else {
            $this->error('An error occurred please check your configuration.');
        }
        return;
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
