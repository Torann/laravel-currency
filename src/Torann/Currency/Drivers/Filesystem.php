<?php

namespace Torann\Currency\Drivers;

use DateTime;
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
     * @param array           $config
     * @param FactoryContract $filesystem
     */
    public function __construct(array $config, FactoryContract $filesystem)
    {
        parent::__construct($config);

        $this->filesystem = $filesystem->disk($this->getConfig('disk'));
    }

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

        return $this->filesystem->put($path, json_encode($currencies, JSON_PRETTY_PRINT));
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        // Get blacklist path
        $path = $this->getConfig('path');

        // Get contents if file exists
        $contents = $this->filesystem->exists($path)
            ? $this->filesystem->get($path)
            : "{}";

        return json_decode($contents, true);
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
        // Get blacklist path
        $path = $this->getConfig('path');

        // Get all as an array
        $currencies = $this->all();

        // Updated at stamp
        $updated = is_null($timestamp) ? new DateTime('now') : $timestamp;

        if (isset($currencies[$code])) {
            $currencies[$code]['value'] = $value;
            $currencies[$code]['updated_at'] = $updated->format('Y-m-d H:i:s');

            return $this->filesystem->put($path, json_encode($currencies, JSON_PRETTY_PRINT));
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

            return $this->filesystem->put($path, json_encode($currencies, JSON_PRETTY_PRINT));
        }

        return false;
    }
}
