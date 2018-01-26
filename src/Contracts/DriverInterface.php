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
     * @param int    $active
     *
     * @return mixed
     */
    public function find($code, $active = 1);
	
	/**
	 * Update given currency.
	 *
	 * @param string $code
	 * @param array $attributes
	 * @param DateTime $timestamp
	 * @param bool $auto Whether or not it's performed automatically
	 *
	 * @return int
	 */
    public function update($code, array $attributes, DateTime $timestamp = null, $auto = false);

    /**
     * Remove given currency from storage.
     *
     * @return int
     */
    public function delete($code);
}