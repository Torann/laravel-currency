<?php

namespace Torann\Currency\Exceptions;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function invalidApiKey()
    {
        return new static("An invalid API Key was specified.");
    }

    public static function currencyNotSupported($source, $currency = null)
    {
        if ($currency) {
            return new static("Currency [{$currency}] is not supported in [{$source}] source.");
        }

        return new static("Any of your currencies is not supported in [{$source}] source.");
    }
}
