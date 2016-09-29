<?php

namespace Torann\Currency\Contracts;

use DateTime;

interface DriverInterface
{
    /**
     * Create a new currency.
     *
     * @param array $params
     *
     * @return bool
     */
    public function create(array $params);

    /**
     * Get all currencies.
     *
     * @return array
     */
    public function all();

    /**
     * Get given currency from storage.
     *
     * @param string $code
     *
     * @return mixed
     */
    public function find($code);

    /**
     * Update given currency.
     *
     * @param string   $code
     * @param float    $value
     * @param DateTime $timestamp
     *
     * @return int
     */
    public function update($code, $value, DateTime $timestamp = null);

    /**
     * Remove given currency from storage.
     *
     * @return int
     */
    public function delete($code);
}