<?php

if (! function_exists('currency')) {
    /**
     * Format given number.
     *
     * @param float  $number
     * @param null   $currency
     * @param string $symbolStyle
     * @param bool   $inverse
     * @param string $roundingType
     * @param null   $precision
     * @param null   $decimalPlace
     *
     * @return \Torann\Currency\Currency|string
     */
    function currency($number = null, $currency = null, $symbolStyle = '%symbol%', $inverse = false, $roundingType = '', $precision = null, $decimalPlace = null)
    {
        if (is_null($number)) {
            return app('currency');
        }

        return app('currency')->format(
            $number,
            $currency,
            $symbolStyle,
            $inverse,
            $roundingType,
            $precision,
            $decimalPlace
        );
    }
}