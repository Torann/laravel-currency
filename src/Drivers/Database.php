<?php

namespace Torann\Currency\Drivers;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Database\DatabaseManager;

class Database extends AbstractDriver
{
    /**
     * Database manager instance.
     *
     * @var DatabaseManager
     */
    protected $database;

    /**
     * Create a new driver instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->database = app('db')->connection($this->config('connection'));
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $params)
    {
        // Ensure the currency doesn't already exist
        if ($this->find($params['code'], null) !== null) {
            return 'exists';
        }

        // Created at stamp
        $created = new DateTime('now');

        $params = array_merge([
            'name' => '',
            'code' => '',
            'symbol' => '',
            'format' => '',
            'exchange_rate' => 1,
            'active' => 1,
            'created_at' => $created,
            'updated_at' => $created,
        ], $params);

        return $this->database->table($this->config('table'))->insert($params);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $collection = new Collection($this->database->table($this->config('table'))->get());

        return $collection->keyBy('code')
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'code' => strtoupper($item->code),
                    'symbol' => $item->symbol,
                    'format' => $item->format,
                    'exchange_rate' => $item->exchange_rate,
                    'active' => $item->active,
                    'created_at' => $item->updated_at,
                    'updated_at' => $item->updated_at,
                ];
            })
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function find($code, $active = 1)
    {
        $query = $this->database->table($this->config('table'))
            ->where('code', strtoupper($code));

        // Make active optional
        if (is_null($active) === false) {
            $query->where('active', $active);
        }

        return $query->first();
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, array $attributes, DateTime $timestamp = null)
    {
        $table = $this->config('table');

        // Create timestamp
        if (empty($attributes['updated_at']) === true) {
            $attributes['updated_at'] = new DateTime('now');
        }

        return $this->database->table($table)
            ->where('code', strtoupper($code))
            ->update($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        $table = $this->config('table');

        return $this->database->table($table)
            ->where('code', strtoupper($code))
            ->delete();
    }
}
