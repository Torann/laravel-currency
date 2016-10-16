# Currency for Laravel

[![Latest Stable Version](https://poser.pugx.org/torann/currency/v/stable.png)](https://packagist.org/packages/torann/currency)
[![Total Downloads](https://poser.pugx.org/torann/currency/downloads.png)](https://packagist.org/packages/torann/currency)
[![Patreon donate button](https://img.shields.io/badge/patreon-donate-yellow.svg)](https://www.patreon.com/torann)
[![Donate weekly to this project using Gratipay](https://img.shields.io/badge/gratipay-donate-yellow.svg)](https://gratipay.com/~torann)
[![Donate to this project using Flattr](https://img.shields.io/badge/flattr-donate-yellow.svg)](https://flattr.com/profile/torann)
[![Donate to this project using Paypal](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4CJA2A97NPYVU)

This provides Laravel with currency functions such as currency formatting and conversion using up-to-date exchange rates.

- [Currency on Packagist](https://packagist.org/packages/torann/currency)
- [Currency on GitHub](https://github.com/torann/laravel-currency)

## Official Documentation

Documentation for the package can be found on [Lyften.com](http://lyften.com/projects/laravel-currency/).

## Laravel 4

For Laravel 4 Installation see [version 0.1](https://github.com/Torann/laravel-currency/tree/0.1);

## Change Log

#### v1.0.1

- Conditional format on conversion
- Fixed bug #51
- Fixed bug #52
- Fixed bug #54

#### v1.0.0

- Major overhaul
- 118 currencies added
- Added support for custom formatters [See repo](https://github.com/Torann/laravel-currency/tree/master/src/Torann/Currency/Formatters)
- Rebuild cache after exchange rate updates

#### v0.3.0

- Fix [Bug 33](https://github.com/Torann/laravel-currency/issues/33)
- Fix Polish currency

#### v0.2.1

- Force conversion to number [Bug 25](https://github.com/Torann/laravel-currency/issues/25)
- Made it easier to use the drivers

#### v0.2.0

- Support Laravel 5
- Update the code to PSR-4 compliant
- Add custom storage drivers
- Removed cookie support
- Removed Blade custom extension (use helper function `currency()`)

#### v0.1.3

- Code cleanup

#### v0.1.2

- Bug fixes

#### v0.1.1

- Added support for OpenExchangeRates.org
- Added a cleanup Artisan command
- Refactored caching
- Fixed bug in the commands

#### v0.1.0

- First release