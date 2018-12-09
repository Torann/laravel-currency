<?php

namespace Torann\Currency\Contracts;

interface Source
{
    /**
     * Fetch the exchange rates for the default currency.
     *
     * @return array
     */
    public function fetch();
}
