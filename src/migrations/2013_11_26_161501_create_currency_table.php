<?php

use Illuminate\Database\Migrations\Migration;

class CreateCurrencyTable extends Migration
{
    /**
     * Currencies table name
     *
     * @var string
     */
    protected $table_name;

    /**
     * Create a new migration instance.
     */
    public function __construct()
    {
        $this->table_name = config('currency.drivers.database.table');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table_name, function ($table) {
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('code', 10)->index();
            $table->string('symbol', 25);
            $table->string('format', 50);
            $table->string('exchange_rate');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        $this->insertData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table_name);
    }

    /**
     * Insert currency data.
     *
     * Formatting data from:
     * http://www.thefinancials.com/Default.aspx?SubSectionID=curformat
     *
     * @return void
     */
    private function insertData()
    {
        DB::table($this->table_name)->insert([
            [
                'id' => 1,
                'name' => 'U.S. Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'format' => '$1,0.00',
                'exchange_rate' => 1.00000000,
                'active' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 2,
                'name' => 'Euro',
                'symbol' => '€',
                'code' => 'EUR',
                'format' => '€1,0.00',
                'exchange_rate' => 0.74970001,
                'active' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 3,
                'name' => 'Pound Sterling',
                'symbol' => '£',
                'code' => 'GBP',
                'format' => '£1,0.00',
                'exchange_rate' => 0.62220001,
                'active' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 4,
                'name' => 'Australian Dollar',
                'symbol' => '$',
                'code' => 'AUD',
                'format' => '$1,0.00',
                'exchange_rate' => 0.94790000,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 5,
                'name' => 'Canadian Dollar',
                'symbol' => '$',
                'code' => 'CAD',
                'format' => '$1,0.00',
                'exchange_rate' => 0.98500001,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 6,
                'name' => 'Czech Koruna',
                'symbol' => 'Kč',
                'code' => 'CZK',
                'format' => 'Kč1.0,00',
                'exchange_rate' => 19.16900063,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 7,
                'name' => 'Danish Krone',
                'symbol' => 'kr',
                'code' => 'DKK',
                'format' => 'kr1.0,00',
                'exchange_rate' => 5.59420013,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 8,
                'name' => 'Hong Kong Dollar',
                'symbol' => '$',
                'code' => 'HKD',
                'format' => '$1,0.00',
                'exchange_rate' => 7.75290012,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 9,
                'name' => 'Hungarian Forint',
                'symbol' => 'Ft',
                'code' => 'HUF',
                'format' => 'Ft1.000', // This needs verifying
                'exchange_rate' => 221.27000427,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 10,
                'name' => 'Israeli New Sheqel',
                'symbol' => '₪',
                'code' => 'ILS',
                'format' => '₪1,0.00', // This needs verifying
                'exchange_rate' => 3.73559999,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 11,
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'code' => 'JPY',
                'format' => '¥1,0',
                'exchange_rate' => 88.76499939,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 12,
                'name' => 'Mexican Peso',
                'symbol' => '$',
                'code' => 'MXN',
                'format' => '$1,0.00',
                'exchange_rate' => 12.63899994,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 13,
                'name' => 'Norwegian Krone',
                'symbol' => 'kr',
                'code' => 'NOK',
                'format' => 'kr1.0,00',
                'exchange_rate' => 5.52229977,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 14,
                'name' => 'New Zealand Dollar',
                'symbol' => '$',
                'code' => 'NZD',
                'format' => '$1,0.00',
                'exchange_rate' => 1.18970001,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 15,
                'name' => 'Philippine Peso',
                'symbol' => '₱',
                'code' => 'PHP',
                'format' => '₱1,0.00',
                'exchange_rate' => 40.58000183,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 16,
                'name' => 'Polish Zloty',
                'symbol' => 'zł',
                'code' => 'PLN',
                'format' => '1 0,00zł',
                'exchange_rate' => 3.08590007,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 17,
                'name' => 'Singapore Dollar',
                'symbol' => '$',
                'code' => 'SGD',
                'format' => '$1,0.00',
                'exchange_rate' => 1.22560000,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 18,
                'name' => 'Swedish Krona',
                'symbol' => 'kr',
                'code' => 'SEK',
                'format' => 'kr1 0,00',
                'exchange_rate' => 6.45870018,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 19,
                'name' => 'Swiss Franc',
                'symbol' => 'CHF',
                'code' => 'CHF',
                'format' => 'CHF1\'0.00',
                'exchange_rate' => 0.92259997,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 20,
                'name' => 'Taiwan New Dollar',
                'symbol' => 'NT$',
                'code' => 'TWD',
                'format' => 'NT$1,0.00',
                'exchange_rate' => 28.95199966,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 21,
                'name' => 'Thai Baht',
                'symbol' => '฿',
                'code' => 'THB',
                'format' => '฿1,0.00',
                'exchange_rate' => 30.09499931,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 22,
                'name' => 'Ukrainian hryvnia',
                'symbol' => '₴',
                'code' => 'UAH',
                'format' => '₴1 0,00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 23,
                'name' => 'Icelandic króna',
                'symbol' => 'kr',
                'code' => 'ISK',
                'format' => 'kr1.000',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 24,
                'name' => 'Croatian kuna',
                'symbol' => 'kn',
                'code' => 'HRK',
                'format' => 'kn1.0,00', // This needs verifying
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 25,
                'name' => 'Romanian leu',
                'symbol' => 'lei',
                'code' => 'RON',
                'format' => '1.0,00lei', // This needs verifying
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 26,
                'name' => 'Bulgarian lev',
                'symbol' => 'лв.',
                'code' => 'BGN',
                'format' => 'лв1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 27,
                'name' => 'Turkish lira',
                'symbol' => '₺',
                'code' => 'TRY',
                'format' => '₺1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 28,
                'name' => 'Chilean peso',
                'symbol' => '$',
                'code' => 'CLP',
                'format' => '$1!0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 29,
                'name' => 'South African rand',
                'symbol' => 'R',
                'code' => 'ZAR',
                'format' => 'R1 0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 30,
                'name' => 'Brazilian real',
                'symbol' => 'R$',
                'code' => 'BRL',
                'format' => 'R$1.0,00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 31,
                'name' => 'Malaysian ringgit',
                'symbol' => 'RM',
                'code' => 'MYR',
                'format' => 'RM1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 32,
                'name' => 'Russian ruble',
                'symbol' => '₽',
                'code' => 'RUB',
                'format' => '₽1.0,00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 33,
                'name' => 'Indonesian rupiah',
                'symbol' => 'Rp',
                'code' => 'IDR',
                'format' => 'Rp1.0,00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 34,
                'name' => 'Indian rupee',
                'symbol' => '₹',
                'code' => 'INR',
                'format' => '₹1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 35,
                'name' => 'Korean won',
                'symbol' => '₩',
                'code' => 'KRW',
                'format' => '₩1,0',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 36,
                'name' => 'Renminbi',
                'symbol' => '¥',
                'code' => 'CNY',
                'format' => '¥1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
        ]);
    }
}
