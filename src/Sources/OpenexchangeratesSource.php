<?php

namespace Torann\Currency\Sources;

use \DateTime;
use Torann\Currency\Contracts\Source as SourceContract;
use Torann\Currency\Exceptions\InvalidArgumentException;
use Torann\Currency\Exceptions\ConnectionFailedException;

class OpenexchangeratesSource extends Source implements SourceContract
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
     * @throws \Torann\Currency\Exceptions\LimitationException
     * @throws \Torann\Currency\Exceptions\InvalidArgumentException
     * @throws \Torann\Currency\Exceptions\ConnectionFailedException
     */
    public function fetch()
    {
        $response = json_decode(
            $this->request($url = $this->baseUrl."/latest.json?".http_build_query($this->queryParameters())
        ), true);

        if (isset($response['error'])) {
            throw new InvalidArgumentException($response['description']);
        }

        if (! $response || ! isset($response['rates'])) {
            throw ConnectionFailedException::source($this->name());
        }

        $updatedAt = (new DateTime)->setTimestamp($response['timestamp']);

        return collect($response['rates'])->mapWithKeys(function ($rate, $currencyCode) use ($updatedAt) {
            return $this->formatRate($currencyCode, $rate, $updatedAt);
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
            'app_id' => $this->accessKey,
            'base' => $this->defaultCurrency,
            'symbols' => implode(',', $this->currencies()),
        ];
    }
}
