<?php

namespace Torann\Currency\Drivers;

use DateTime;
use Illuminate\Support\Arr;

abstract class AbstractDriver
{
    /**
     * Driver config
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new driver instance.
     *
     * @param array  $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Create a new currency.
     *
     * @param array $params
     * @return bool
     */
    abstract public function create(array $params);

    /**
     * Get all currencies.
     *
     * @return array
     */
    abstract public function all();

    /**
     * Get given currency from storage.
     *
     * @param string $code
     *
     * @return mixed
     */
    abstract public function find($code);

    /**
     * Update given currency.
     *
     * @param string   $code
     * @param float    $value
     * @param DateTime $timestamp
     *
     * @return int
     */
    abstract public function update($code, $value, DateTime $timestamp = null);

    /**
     * Remove given currency from storage.
     *
     * @return int
     */
    abstract public function delete($code);
}