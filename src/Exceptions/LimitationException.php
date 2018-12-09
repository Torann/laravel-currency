<?php

namespace Torann\Currency\Exceptions;

use \Exception;

class LimitationException extends Exception
{
    public static function apiRequestsLimitReached()
    {
        return new static("The maximum allowed API amount of monthly API requests has been reached.");
    }

    public static function subscriptionPlanLimited($message = null)
    {
        return new static($message ?: "The current subscription plan does not support this API endpoint.");
    }
}
