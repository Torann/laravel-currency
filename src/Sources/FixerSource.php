<?php

namespace Torann\Currency\Sources;

use Torann\Currency\Contracts\SourceInterface;
use Torann\Currency\Exceptions\LimitationException;
use Torann\Currency\Exceptions\InvalidArgumentException;
use Torann\Currency\Exceptions\ConnectionFailedException;

class FixerSource extends Source implements SourceInterface
{
    /**
     * Base URL of external API service.
     *
     * @var string
     */
    protected $baseUrl = 'http://data.fixer.io/api';

    /**
     * Get an array of exchange rates for the default currency.
     *
     * @return array
     *
     * @throws \Torann\Currency\Exceptions\LimitationException
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     * @throws \Torann\Currency\Exceptions\ConnectionFailedException
     */
    public function fetch()
    {
        $response = $this->responseToArray(
            $this->request($this->baseUrl."/latest?".http_build_query($this->queryParameters())
        ));

        if (isset($response['error']['code'])) {
            $this->handleErrors($response);
        }

        if (isset($response['success']) && $response['success']) {
            return collect($response['rates'])->mapWithKeys(function ($rate, $currencyCode) {
                return $this->formatRate($currencyCode, $rate);
            })->except($this->defaultCurrency)->all();
        }

        throw ConnectionFailedException::source($this->name());
    }

    /**
     * Get the query parameters.
     *
     * @return array
     */
    protected function queryParameters()
    {
        return [
            'access_key' => $this->accessKey,
            'base' => $this->defaultCurrency,
            'symbols' => implode(',', $this->currencies()),
        ];
    }

    /**
     * Throw an exception if response has error.
     *
     * @param  array  $response
     * @return void
     *
     * @throws \Torann\Currency\Exceptions\LimitationException
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     */
    protected function handleErrors(array $response)
    {
        switch ($response['error']['code']) {
            case 101:
                // No API Key was specified or an invalid API Key was specified.
                throw InvalidArgumentException::invalidApiKey();
            case 104:
                // The maximum allowed API amount of monthly API requests has been reached.
                throw LimitationException::apiRequestsLimitReached();
            case 105:
                // The current subscription plan does not support this API endpoint.
                if ($this->defaultCurrency === "EUR") {
                    throw LimitationException::subscriptionPlanLimited();
                }

                throw LimitationException::subscriptionPlanLimited("Free subscription plan of [{$this->name()}] source supports only EUR as base currency.");
            case 201:
                // An invalid base currency has been entered.
                throw InvalidArgumentException::currencyNotSupported($this->name(), $this->defaultCurrency);
            case 202:
                // One or more invalid symbols have been specified.
                throw InvalidArgumentException::currencyNotSupported($this->name());
        }
    }
}
