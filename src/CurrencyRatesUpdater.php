<?php

namespace Torann\Currency;

class CurrencyRatesUpdater
{
    /**
     * The source manager instance.
     *
     * @var \Torann\Currency\SourceManager
     */
    protected $manager;

    /**
     * The Currency instance.
     *
     * @var \Torann\Currency\Currency
     */
    protected $currency;

    /**
     * Create a new instance.
     *
     * @param  \Torann\Currency\SourceManager  $manager
     * @return void
     */
    public function __construct(SourceManager $manager)
    {
        $this->manager = $manager;

        $this->currency = app('currency');
    }

    /**
     * Update the exchange rates.
     *
     * @return void
     */
    public function update()
    {
        foreach ($this->fetchRates() as $code => $currency) {
            $this->updateRateFor($code, $currency['rate'], $currency['updated_at']);

            $this->clearCache();
        }
    }

    /**
     * Fetch the exchange rates.
     *
     * @return array
     */
    protected function fetchRates()
    {
        return $this->manager->source()->fetch();
    }

    /**
     * Update rate.
     *
     * @param  string  $code
     * @param  mixed  $rate
     * @param  \DateTimeInterface|string  $updatedAt
     * @return int
     */
    protected function updateRateFor($code, $rate, $updatedAt)
    {
        return $this->currency->getDriver()->update($code, [
            'exchange_rate' => $rate,
            'updated_at' => $updatedAt,
        ]);
    }

    /**
     * Clear the currency cache.
     *
     * @return void
     */
    protected function clearCache()
    {
        $this->currency->clearCache();
    }
}
