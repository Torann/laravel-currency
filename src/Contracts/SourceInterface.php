<?php

namespace Torann\Currency\Contracts;

interface SourceInterface
{
    /**
     * Fetch the exchange rates for the default currency.
     *
     * @return array
     */
    public function fetch();
}
