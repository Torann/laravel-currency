<?php

namespace Torann\Currency\Sources;

use Torann\Currency\Contracts\SourceInterface;
use Torann\Currency\Exceptions\InvalidArgumentException;
use Torann\Currency\Exceptions\ConnectionFailedException;

class ExchangeratesapiSource extends Source implements SourceInterface
{
    /**
     * Base URL of external API service.
     *
     * @var string
     */
    protected $baseUrl = 'https://api.exchangeratesapi.io';

    /**
     * Get an array of exchange rates for the default currency.
     *
     * @return array
     *
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     * @throws \Torann\Currency\Exceptions\ConnectionFailedException
     */
    public function fetch()
    {
        $response = $this->responseToArray(
            $this->request($this->baseUrl."/latest?".http_build_query($this->queryParameters())
        ));

        if (isset($response['error'])) {
            $this->handleErrors($response);
        }

        if (isset($response['rates'])) {
            return collect($response['rates'])->mapWithKeys(function ($rate, $currencyCode) {
                return $this->formatRate($currencyCode, $rate);
            })->except($this->defaultCurrency)->all();
        };

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
            'base' => $this->defaultCurrency,
            'currencies' => collect($this->currencies())->implode(','),
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
    protected function handleErrors($response)
    {
        if (str_contains($response['error'], 'not supported')) {
            throw InvalidArgumentException::currencyNotSupported($this->name(), $this->defaultCurrency);
        }

        if (str_contains($response['error'], 'invalid')) {
            throw InvalidArgumentException::currencyNotSupported($this->name());
        }
    }
}
