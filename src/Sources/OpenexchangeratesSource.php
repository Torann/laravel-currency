<?php

namespace Torann\Currency\Sources;

use \DateTime;
use Torann\Currency\Contracts\SourceInterface;
use Torann\Currency\Exceptions\InvalidArgumentException;
use Torann\Currency\Exceptions\ConnectionFailedException;

class OpenexchangeratesSource extends Source implements SourceInterface
{
    /**
     * Base URL of external API service.
     *
     * @var string
     */
    protected $baseUrl = 'https://openexchangerates.org/api';

    /**
     * Get an array of exchange rates for the default currency.
     *
     * @return array
     *
     * @throws \Exception
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     * @throws \Torann\Currency\Exceptions\ConnectionFailedException
     */
    public function fetch()
    {
        $response = $this->responseToArray(
            $this->request($this->baseUrl."/latest.json?".http_build_query($this->queryParameters())
        ));

        if (isset($response['error'])) {
            $this->handleErrors($response);
        }

        if (isset($response['rates'])) {
            $updatedAt = (new DateTime)->setTimestamp($response['timestamp']);

            return collect($response['rates'])->mapWithKeys(function ($rate, $currencyCode) use ($updatedAt) {
                return $this->formatRate($currencyCode, $rate, $updatedAt);
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
            'app_id' => $this->accessKey,
            'base' => $this->defaultCurrency,
            'symbols' => collect($this->currencies())->implode(','),
        ];
    }

    /**
     * Throw an exception if response has error.
     *
     * @param  array  $response
     * @return void
     *
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     */
    protected function handleErrors(array $response)
    {
        throw new InvalidArgumentException($response['description']);
    }
}
