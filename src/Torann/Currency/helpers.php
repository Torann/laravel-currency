<?php

if (!function_exists('currency')) {
    /**
     * Convert given number.
     *
     * @param float  $amount
     * @param string $from
     * @param string $to
     *
     * @return \Torann\Currency\Currency|string
     */
    function currency($amount = null, $from = null, $to = null)
    {
        if (is_null($amount)) {
            return app('currency');
        }

        return app('currency')->convert($amount, $from, $to);
    }
}

if (!function_exists('currency_format')) {
    /**
     * Format given number.
     *
     * @param float  $amount
     * @param string $currency
     *
     * @return string
     */
    function currency_format($amount = null, $currency = null)
    {
        return app('currency')->format($amount, $currency);
    }
}