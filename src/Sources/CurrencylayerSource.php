<?php

namespace Torann\Currency\Sources;

use DateTimeInterface;
use Torann\Currency\Contracts\SourceInterface;
use Torann\Currency\Exceptions\LimitationException;
use Torann\Currency\Exceptions\InvalidArgumentException;
use Torann\Currency\Exceptions\ConnectionFailedException;

class CurrencylayerSource extends Source implements SourceInterface
{
    /**
     * Base URL of external API service.
     *
     * @var string
     */
    protected $baseUrl = 'http://www.apilayer.net/api/live';

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
            $this->request($this->baseUrl."?".http_build_query($this->queryParameters()))
        );

        if (isset($response['error']['code'])) {
            $this->handleErrors($response);
        }

        if (isset($response['success']) && $response['success'] && isset($response['quotes'])) {
            return collect($response['quotes'])->mapWithKeys(function ($rate, $currencyCode) {
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
            'source' => $this->defaultCurrency,
            'currencies' => collect($this->currencies())->implode(','),
        ];
    }

    /**
     * Format the given currency code and rate.
     *
     * @param  string  $currencyCode
     * @param  mixed  $rate
     * @param  \DateTimeInterface|null  $updatedAt
     * @return array
     *
     * @throws \Exception
     */
    protected function formatRate($currencyCode, $rate, DateTimeInterface $updatedAt = null)
    {
        // Because of currency code now has format like "USDEUR",
        // we need to remove base currency to have only "EUR".
        $currencyCode = str_replace($this->defaultCurrency, '', $currencyCode);

        return parent::formatRate($currencyCode, $rate);
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
    protected function handleErrors($response)
    {
        switch ($response['error']['code']) {
            case 101:
                // User did not supply an access key or supplied an invalid access key.
                throw InvalidArgumentException::invalidApiKey($this->name());
            case 102:
                // The user's account is not active. User will be prompted to get in touch with Customer Support.
                throw new LimitationException($response['error']['info']);
            case 104:
                // User has reached or exceeded his subscription plan's monthly API request allowance.
                throw LimitationException::apiRequestsLimitReached();
            case 105:
                // The user's current subscription plan does not support the requested API function.
                if ($this->defaultCurrency === "USD") {
                    throw LimitationException::subscriptionPlanLimited();
                }

                throw LimitationException::subscriptionPlanLimited($response['error']['info']);
            case 201:
                // User entered an invalid Source Currency.
                throw InvalidArgumentException::currencyNotSupported($this->name(), $this->defaultCurrency);
            case 202:
                // User entered one or more invalid currency codes.
                throw InvalidArgumentException::currencyNotSupported($this->name());
        }
    }
}
