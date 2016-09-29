# Basic usage

## Rendering

Using the helper function

```php
<?php

echo currency(12.00, 'USD');
```

- The first parameter is the amount.
- *optional* The second parameter is the ISO 4217 currency code. If not set it will use the default set in the config file.

```php
echo Currency::format(12.00, 'USD');
```