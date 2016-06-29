<?php

namespace Torann\Currency\Middleware;

use Closure;
use Illuminate\Http\Request;

class CurrencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Don't redirect the console
        if ($this->runningInConsole()) {
            return $next($request);
        }

        // Check for a user defined currency
        if ($request->get('currency') && currency()->hasCurrency($request->get('currency'))) {
            $currency = $request->get('currency');
        }
        elseif ($request->getSession()->get('currency')
            && currency()->hasCurrency($request->getSession()->get('currency'))
        ) {
            $currency = $request->getSession()->get('currency');
        }
        else {
            $currency = currency()->getConfig('default');
        }

        // Set user currency
        $this->setUserCurrency($currency, $request);

        return $next($request);
    }

    /**
     * Determine if the application is running in the console.
     *
     * @return bool
     */
    protected function runningInConsole()
    {
        return app()->runningInConsole();
    }

    /**
     * Set the user currency.
     *
     * @param string  $currency
     * @param Request $request
     */
    protected function setUserCurrency($currency, $request)
    {
        currency()->setCurrency($currency);

        $request->getSession()->put(['currency' => $currency]);
        $request->getSession()->reflash();
    }
}