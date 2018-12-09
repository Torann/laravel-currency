<?php

namespace Torann\Currency\Exceptions;

class ConnectionFailedException extends \Exception
{
    public static function source($source)
    {
        return new static("Connection to [{$source}] source was failed.");
    }
}
