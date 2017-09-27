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
     * Get all active currencies.
     *
     * @return array
     */
    public function allActive();

    /**
     * Get given currency from storage.
     *
     * @param string $code
     * @param int    $active
     *
     * @return array|null
     */
    public function find($code, $active = 1);

    /**
     * Update given currency.
     *
     * @param string   $code
     * @param array    $attributes
     * @param DateTime $timestamp
     *
     * @return int
     */
    public function update($code, array $attributes, DateTime $timestamp = NULL);

    /**
     * Activate given currency.
     *
     * @param  string  $code
     * @return int
     */
    public function activate($code);

    /**
     * Deactivate given currency.
     *
     * @param  string  $code
     * @return int
     */
    public function deactivate($code);

    /**
     * Remove given currency from storage.
     *
     * @return int
     */
    public function delete($code);
}
