# Storage Drivers

### Database (default)

Which database table to used is specified in the config file in the **drivers** section under `database`.

```php
'driver' => 'database',
```

### Laravel Filesystem

By using Laravel's built in Filesystem we have the ability to store the currency data in the cloud. Which file to used is specified in the config file in the **drivers** section under `filesystem`, along with which disk to use `null` will use the system default disk.

```php
'driver' => 'filesystem',
```

### Custom Driver

Drivers are stored in the Currency's config file `config/currency.php`. Simple update the `driver` with the name of you custom driver and add it to the `drivers` specific configuration section with the `class` value as the custom classname.

**Example driver**

```php
<?php

namespace App\Currency\Drivers;

use DateTime;
use Illuminate\Support\Arr;
use Torann\Currency\Drivers\AbstractDriver;

class Local extends AbstractDriver
{
    /**
     * {@inheritdoc}
     */
    public function create(array $params)
    {
        // Get blacklist path
        $path = $this->getConfig('path');

        // Get all as an array
        $currencies = $this->all();

        // Created at stamp
        $created = new DateTime('now');

        $currencies[$params['code']] = array_merge([
            'title' => '',
            'symbol_left' => '',
            'symbol_right' => '',
            'code' => '',
            'decimal_place' => 2,
            'value' => 1.00000000,
            'decimal_point' => '.',
            'thousand_point' => ',',
            'status' => 0,
            'created_at' => $created->format('Y-m-d H:i:s'),
            'updated_at' => $created->format('Y-m-d H:i:s'),
        ], $params);

        return file_put_contents($path, json_encode($currencies));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $path = $this->getConfig('path');

        return json_decode(file_get_contents($path), true);
    }

    /**
     * {@inheritdoc}
     */
    public function find($code)
    {
        return Arr::get($this->all(), $code);
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, $value, DateTime $timestamp = null)
    {
        $path = $this->getConfig('path');

        $currencies = json_decode(file_get_contents($path), true);

        // Update given code
        if (isset($currencies[$code])) {
            $currencies[$code]['value'] = $value;
            $currencies[$code]['updated_at'] = new DateTime('now');

            return file_put_contents($path, json_encode($currencies));
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        // Get blacklist path
        $path = $this->getConfig('path');

        // Get all as an array
        $currencies = $this->all();

        if (isset($currencies[$code])) {
            unset($currencies[$code]);

            return file_put_contents($path, json_encode($currencies));
        }

        return false;
    }
}
```

**In the config file**

```php
    'driver' => 'local',

    'drivers' => [

        ...

        'local' => [
            'class' => \App\Currency\Drivers\Local::class,
            'path'  => base_path('currencies.json'),
        ],

    ],
```