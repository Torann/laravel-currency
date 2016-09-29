# Methods

The simplest way to use these method is though the helper function `currency()` or by using the facade. For the examples below we will use the helper method.

## Formatting a Currency Shortcut

This is a shortcut to the most commonly used `format` method.

```php
currency($number, $currency, $symbolStyle, $inverse, $roundingType, $precision, $decimalPlace)
```

**Arguments:**

- $number
- $currency _(default null)_
- $symbolStyle _(default %symbol%)_
- $inverse _(default false)_
- $roundingType _(default '')_
- $precision _(default null)_
- $decimalPlace _(default null)_

**Usage:**

```php
echo currency(12.00);        // Will format the value using the user selected currency
echo currency(12.00, 'EUR'); // Will format the value from the default currency to EUR
```

> **NOTE:** If the `$currency` argument is not set it will use the user selected currency.

## The Power of the `currency()` Function

When the currency function is used without arguments it will return the `\Torann\Currency\Currency` instance, and with this we can do all types of amazing things.

### `hasCurrency($code)`

Determine if given currency exists.

```php
currency()->hasCurrency('EUR');
```

### `setCurrency($code)`

Set user's currency.

```php
currency()->setCurrency('EUR');
```

### `getCurrencyCode()`

Return the user's currency code.

```php
currency()->getCurrencyCode();
```

### `getCurrency($code = null)`

Return the current currency if the one supplied is not valid

```php
currency()->getCurrency();
```

### `clearCache()`

Clear all cached currencies.

```php
currency()->clearCache();
```

### `getConfig($key, $default = null)`

Get a currency configuration value.

```php
currency()->getConfig('default');
```

### `create(array $params)`

Create a currency using the default driver.

```php
currency()->create([
    'title' => 'U.S. Dollar',
    'symbol_left' => '$',
    'symbol_right' => '',
    'decimal_place': 2,
    'value' => '1.0000',
    'decimal_point' => '.',
    'thousand_point' => ',',
    'code' => 'USD',
]);
```

### `find($code)`

Get the provided currency using the default driver.

```php
currency()->find('USD');
```

### `update($code, $value, DateTime $timestamp = null)`

Update the provided currency's value using the default driver

```php
currency()->update('USD', 1.22);
```

### `delete($code)`

Delete the provided currency using the default driver.

```php
currency()->delete('USD');
```

### `getDriver()`

This method returns the default storage driver instance used.

```php
currency()->getDriver();
```