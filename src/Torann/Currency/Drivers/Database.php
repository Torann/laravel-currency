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
        $table = $this->getConfig('table', 'currencies');

        $params = array_merge([
            'title' => '',
            'symbol_left' => '',
            'symbol_right' => '',
            'code' => '',
            'decimal_place' => 2,
            'value' => 1.00000000,
            'decimal_point' => '.',
            'thousand_point' => ',',
            'status' => 0,
            'created_at' => new DateTime('now'),
            'updated_at' => new DateTime('now'),
        ], $params);

        return $this->database->table($table)->insert($params);
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $cache = [];

        $table = $this->getConfig('table', 'currencies');

        foreach ($this->database->table($table)->get() as $currency) {
            $cache[$currency->code] = [
                'id' => $currency->id,
                'title' => $currency->title,
                'symbol_left' => $currency->symbol_left,
                'symbol_right' => $currency->symbol_right,
                'decimal_place' => $currency->decimal_place,
                'value' => $currency->value,
                'decimal_point' => $currency->decimal_point,
                'thousand_point' => $currency->thousand_point,
                'code' => $currency->code,
            ];
        }

        return $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function find($code)
    {
        $table = $this->getConfig('table', 'currencies');

        return $this->database->table($table)
            ->where('code', $code)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function update($code, $value, DateTime $timestamp = null)
    {
        $table = $this->getConfig('table', 'currencies');

        // Create timestamp
        $timestamp = is_null($timestamp) ? new DateTime('now') : $timestamp;

        return $this->database->table($table)
            ->where('code', $code)
            ->update([
                'value' => $value,
                'updated_at' => $timestamp,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($code)
    {
        $table = $this->getConfig('table', 'currencies');

        return $this->database->table($table)
            ->where('code', $code)
            ->delete();
    }
}