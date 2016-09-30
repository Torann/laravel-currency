<?php

namespace Torann\Currency\Drivers;

use DateTime;
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
     * @param array           $config
     * @param DatabaseManager $database
     */
    public function __construct(array $config, DatabaseManager $database)
    {
        parent::__construct($config);

        $this->database = $database->connection($this->getConfig('connection'));
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $params)
    {
        $table = $this->getConfig('table');

        // Created at stamp
        $created = new DateTime('now');

        $params = array_merge([
            'name' => '',
            'code' => '',
            'symbol' => '',
            'format' => '',
            'exchange_rate' => 1,
            'active' => 0,
            'created_at' => $created,
            'updated_at' => $created,
        ], $params);

        return $this->database->table($table)->insert($params);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $table = $this->getConfig('table');

        return $this->database->table($table)
            ->where('active', 1)
            ->get()
            ->keyBy('code')
            ->map(function($item) {
                return [
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
    public function find($code)
    {
        $table = $this->getConfig('table');

        return $this->database->table($table)
            ->where('code', strtoupper($code))
            ->where('active', 1)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, $value, DateTime $timestamp = null)
    {
        $table = $this->getConfig('table');

        // Create timestamp
        $timestamp = is_null($timestamp) ? new DateTime('now') : $timestamp;

        return $this->database->table($table)
            ->where('code', strtoupper($code))
            ->update([
                'exchange_rate' => $value,
                'updated_at' => $timestamp,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        $table = $this->getConfig('table');

        return $this->database->table($table)
            ->where('code', strtoupper($code))
            ->delete();
    }
}