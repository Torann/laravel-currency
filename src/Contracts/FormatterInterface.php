<?php

namespace Torann\Currency\Contracts;

interface FormatterInterface
{
    /**
     * Format the value into the desired currency.
     *
     * @param float  $value
     * @param string $code
     *
     * @return string
     */
    public function format($value, $code = null);
}