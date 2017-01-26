<?php

namespace Torann\Currency\Drivers;

use DateTime;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Filesystem\Factory as FactoryContract;

class Filesystem extends AbstractDriver
{
    /**
     * Database manager instance.
     *
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * Create a new driver instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->filesystem = app('filesystem')->disk($this->config('disk'));
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $params)
    {
        // Get blacklist path
        $path = $this->config('path');

        // Get all as an array
        $currencies = $this->all();

        // Verify the currency doesn't exists
        if (isset($currencies[$params['code']]) === true) {
            return 'exists';
        }

        // Created at stamp
        $created = (new DateTime('now'))->format('Y-m-d H:i:s');

        $currencies[$params['code']] = array_merge([
            'name' => '',
            'code' => '',
            'symbol' => '',
            'format' => '',
            'exchange_rate' => 1,
            'active' => 0,
            'created_at' => $created,
            'updated_at' => $created,
        ], $params);

        return $this->filesystem->put($path, json_encode($currencies, JSON_PRETTY_PRINT));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        // Get blacklist path
        $path = $this->config('path');

        // Get contents if file exists
        $contents = $this->filesystem->exists($path)
            ? $this->filesystem->get($path)
            : "{}";

        return json_decode($contents, true);
    }

    /**
     * {@inheritdoc}
     */
    public function find($code, $active = 1)
    {
        $currency = Arr::get($this->all(), $code);

        // Skip active check
        if (is_null($active)) {
            return $currency;
        }

        return Arr::get($currency, 'active', 1) ? $currency : null;
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $attributes, DateTime $timestamp = null)
    {
        // Get blacklist path
        $path = $this->config('path');

        // Get all as an array
        $currencies = $this->all();

        // Verify the currency exists
        if (isset($currencies[$code]) === false) {
            return 'doesn\'t exists';
        }

        // Create timestamp
        if (empty($attributes['updated_at']) === true) {
            $attributes['updated_at'] = (new DateTime('now'))->format('Y-m-d H:i:s');
        }

        // Merge values
        $currencies[$code] = array_merge($currencies[$code], $attributes);

        return $this->filesystem->put($path, json_encode($currencies, JSON_PRETTY_PRINT));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        // Get blacklist path
        $path = $this->config('path');

        // Get all as an array
        $currencies = $this->all();

        // Verify the currency exists
        if (isset($currencies[$code]) === false) {
            return false;
        }

        unset($currencies[$code]);

        return $this->filesystem->put($path, json_encode($currencies, JSON_PRETTY_PRINT));
    }
}
