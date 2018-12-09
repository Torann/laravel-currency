<?php

namespace Torann\Currency\Sources;

use Torann\Currency\Contracts\Source as SourceContract;
use Torann\Currency\Exceptions\InvalidArgumentException;
use Torann\Currency\Exceptions\ConnectionFailedException;

class ExchangeratesapiSource extends Source implements SourceContract
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
        $response = json_decode(
            $this->request($this->baseUrl."/latest?".http_build_query($this->queryParameters())
        ), true);

        if (isset($response['error'])) {
            if (str_contains($response['error'], 'not supported')) {
                throw InvalidArgumentException::currencyNotSupported($this->name(), $this->defaultCurrency);
            }

            if (str_contains($response['error'], 'invalid')) {
                throw InvalidArgumentException::currencyNotSupported($this->name());
            }
        }

        if (! $response || ! isset($response['rates'])) {
            throw ConnectionFailedException::source($this->name());
        }

        return collect($response['rates'])->mapWithKeys(function ($rate, $currencyCode) {
            return $this->formatRate($currencyCode, $rate);
        })->except($this->defaultCurrency)->all();
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
            'symbols' => implode(',', $this->currencies()),
        ];
    }
}
