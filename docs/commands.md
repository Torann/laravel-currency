# Artisan Commands

### Updating Exchange

By default exchange rates are updated from Finance Yahoo.com.

```bash
php artisan currency:update
```

To upate from OpenExchangeRates.org

```bash
php artisan currency:update --openexchangerates
```

> Note: An API key is needed to use [OpenExchangeRates.org](http://OpenExchangeRates.org). Add yours to the config file.

### Cleanup

Used to clean the Laravel cached exchanged rates and refresh it from the database. Note that cached exchanged rates are cleared after they are updated using one of the command above.

```bash
php artisan currency:cleanup
```