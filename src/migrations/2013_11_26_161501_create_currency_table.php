<?php

use Illuminate\Database\Migrations\Migration;

class CreateCurrencyTable extends Migration {

	protected $table_name;

	public function __construct()
	{
		$this->table_name = Config::get('currency::table_name');
	}

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the currency table
		Schema::create($this->table_name, function($table)
		{
			$table->increments('id')->unsigned();
			$table->string('title', 255);
			$table->string('symbol_left', 12);
			$table->string('symbol_right', 12);
			$table->string('code', 3);
			$table->integer('decimal_place');
			$table->double('value', 15, 8);
			$table->string('decimal_point', 3);
			$table->string('thousand_point', 3);
			$table->integer('status');
			$table->timestamps();
		});

		$currencies = array(
			array(
				'id' => 1,
				'title' => 'U.S. Dollar',
				'symbol_left' => '$',
				'symbol_right' => '',
				'code' => 'USD',
				'decimal_place' => 2,
				'value' => 1.00000000,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 2,
				'title' => 'Euro',
				'symbol_left' => '€',
				'symbol_right' => '',
				'code' => 'EUR',
				'decimal_place' => 2,
				'value' => 0.74970001,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 3,
				'title' => 'Pound Sterling',
				'symbol_left' => '£',
				'symbol_right' => '',
				'code' => 'GBP',
				'decimal_place' => 2,
				'value' => 0.62220001,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 4,
				'title' => 'Australian Dollar',
				'symbol_left' => '$',
				'symbol_right' => '',
				'code' => 'AUD',
				'decimal_place' => 2,
				'value' => 0.94790000,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 5,
				'title' => 'Canadian Dollar',
				'symbol_left' => '$',
				'symbol_right' => '',
				'code' => 'CAD',
				'decimal_place' => 2,
				'value' => 0.98500001,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 6,
				'title' => 'Czech Koruna',
				'symbol_left' => '',
				'symbol_right' => 'Kč',
				'code' => 'CZK',
				'decimal_place' => 2,
				'value' => 19.16900063,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 7,
				'title' => 'Danish Krone',
				'symbol_left' => 'kr',
				'symbol_right' => '',
				'code' => 'DKK',
				'decimal_place' => 2,
				'value' => 5.59420013,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 8,
				'title' => 'Hong Kong Dollar',
				'symbol_left' => '$',
				'symbol_right' => '',
				'code' => 'HKD',
				'decimal_place' => 2,
				'value' => 7.75290012,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 9,
				'title' => 'Hungarian Forint',
				'symbol_left' => 'Ft',
				'symbol_right' => '',
				'code' => 'HUF',
				'decimal_place' => 2,
				'value' => 221.27000427,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 10,
				'title' => 'Israeli New Sheqel',
				'symbol_left' => '?',
				'symbol_right' => '',
				'code' => 'ILS',
				'decimal_place' => 2,
				'value' => 3.73559999,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 11,
				'title' => 'Japanese Yen',
				'symbol_left' => '¥',
				'symbol_right' => '',
				'code' => 'JPY',
				'decimal_place' => 2,
				'value' => 88.76499939,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 12,
				'title' => 'Mexican Peso',
				'symbol_left' => '$',
				'symbol_right' => '',
				'code' => 'MXN',
				'decimal_place' => 2,
				'value' => 12.63899994,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 13,
				'title' => 'Norwegian Krone',
				'symbol_left' => 'kr',
				'symbol_right' => '',
				'code' => 'NOK',
				'decimal_place' => 2,
				'value' => 5.52229977,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 14,
				'title' => 'New Zealand Dollar',
				'symbol_left' => '$',
				'symbol_right' => '',
				'code' => 'NZD',
				'decimal_place' => 2,
				'value' => 1.18970001,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 15,
				'title' => 'Philippine Peso',
				'symbol_left' => 'Php',
				'symbol_right' => '',
				'code' => 'PHP',
				'decimal_place' => 2,
				'value' => 40.58000183,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 16,
				'title' => 'Polish Zloty',
				'symbol_left' => '',
				'symbol_right' => 'zł',
				'code' => 'PLN',
				'decimal_place' => 2,
				'value' => 3.08590007,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 17,
				'title' => 'Singapore Dollar',
				'symbol_left' => '$',
				'symbol_right' => '',
				'code' => 'SGD',
				'decimal_place' => 2,
				'value' => 1.22560000,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 18,
				'title' => 'Swedish Krona',
				'symbol_left' => 'kr',
				'symbol_right' => '',
				'code' => 'SEK',
				'decimal_place' => 2,
				'value' => 6.45870018,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 19,
				'title' => 'Swiss Franc',
				'symbol_left' => 'CHF',
				'symbol_right' => '',
				'code' => 'CHF',
				'decimal_place' => 2,
				'value' => 0.92259997,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 20,
				'title' => 'Taiwan New Dollar',
				'symbol_left' => 'NT$',
				'symbol_right' => '',
				'code' => 'TWD',
				'decimal_place' => 2,
				'value' => 28.95199966,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			),
			array(
				'id' => 21,
				'title' => 'Thai Baht',
				'symbol_left' => '฿',
				'symbol_right' => '',
				'code' => 'THB',
				'decimal_place' => 2,
				'value' => 30.09499931,
				'decimal_point' => '.',
				'thousand_point' => ',',
				'status' => 1,
				'created_at' => '2013-11-29 19:51:38',
				'updated_at' => '2013-11-29 19:51:38',
			)
		);

		DB::table($this->table_name)->insert($currencies);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Delete the currency table
		Schema::drop($this->table_name);
	}

}
