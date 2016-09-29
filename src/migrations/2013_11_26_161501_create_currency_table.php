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
            $table->string('currency_name');
            $table->string('currency_code', 10)->index();
            $table->string('currency_symbol', 25);
            $table->string('currency_format', 50);
            $table->string('exchange_rate');
            $table->boolean('active')->default(false);
            $table->timestamps();
        });

        // Formatting data from: http://www.thefinancials.com/Default.aspx?SubSectionID=curformat
        $currencies = [
            [
                'id' => 1,
                'currency_name' => 'U.S. Dollar',
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'currency_format' => '$1,0.00',
                'exchange_rate' => 1.00000000,
                'active' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 2,
                'currency_name' => 'Euro',
                'currency_symbol' => '€',
                'currency_code' => 'EUR',
                'currency_format' => '€1,0.00',
                'exchange_rate' => 0.74970001,
                'active' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 3,
                'currency_name' => 'Pound Sterling',
                'currency_symbol' => '£',
                'currency_code' => 'GBP',
                'currency_format' => '£1,0.00',
                'exchange_rate' => 0.62220001,
                'active' => 1,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 4,
                'currency_name' => 'Australian Dollar',
                'currency_symbol' => '$',
                'currency_code' => 'AUD',
                'currency_format' => '$1,0.00',
                'exchange_rate' => 0.94790000,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 5,
                'currency_name' => 'Canadian Dollar',
                'currency_symbol' => '$',
                'currency_code' => 'CAD',
                'currency_format' => '$1,0.00',
                'exchange_rate' => 0.98500001,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 6,
                'currency_name' => 'Czech Koruna',
                'currency_symbol' => '',
                'symbol_right' => 'Kč',
                'currency_code' => 'CZK',
                'currency_format' => 'Kč1.0,00',
                'exchange_rate' => 19.16900063,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 7,
                'currency_name' => 'Danish Krone',
                'currency_symbol' => 'kr',
                'currency_code' => 'DKK',
                'currency_format' => 'kr1.0,00',
                'exchange_rate' => 5.59420013,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 8,
                'currency_name' => 'Hong Kong Dollar',
                'currency_symbol' => '$',
                'currency_code' => 'HKD',
                'currency_format' => '$1,0.00',
                'exchange_rate' => 7.75290012,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 9,
                'currency_name' => 'Hungarian Forint',
                'currency_symbol' => 'Ft',
                'currency_code' => 'HUF',
                'currency_format' => 'Ft1.000', // This needs verifying
                'exchange_rate' => 221.27000427,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 10,
                'currency_name' => 'Israeli New Sheqel',
                'currency_symbol' => '₪',
                'currency_code' => 'ILS',
                'currency_format' => '₪1,0.00', // This needs verifying
                'exchange_rate' => 3.73559999,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 11,
                'currency_name' => 'Japanese Yen',
                'currency_symbol' => '¥',
                'currency_code' => 'JPY',
                'currency_format' => '¥1,0',
                'exchange_rate' => 88.76499939,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 12,
                'currency_name' => 'Mexican Peso',
                'currency_symbol' => '$',
                'currency_code' => 'MXN',
                'currency_format' => '$1,0.00',
                'exchange_rate' => 12.63899994,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 13,
                'currency_name' => 'Norwegian Krone',
                'currency_symbol' => 'kr',
                'currency_code' => 'NOK',
                'currency_format' => 'kr1.0,00',
                'exchange_rate' => 5.52229977,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 14,
                'currency_name' => 'New Zealand Dollar',
                'currency_symbol' => '$',
                'currency_code' => 'NZD',
                'currency_format' => '$1,0.00',
                'exchange_rate' => 1.18970001,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 15,
                'currency_name' => 'Philippine Peso',
                'currency_symbol' => '₱',
                'currency_code' => 'PHP',
                'currency_format' => '₱1,0.00',
                'exchange_rate' => 40.58000183,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 16,
                'currency_name' => 'Polish Zloty',
                'currency_symbol' => 'zł',
                'currency_code' => 'PLN',
                'currency_format' => '1 0,00zł',
                'exchange_rate' => 3.08590007,
                'decimal_point' => ',',
                'thousand_point' => '.',
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 17,
                'currency_name' => 'Singapore Dollar',
                'currency_symbol' => '$',
                'currency_code' => 'SGD',
                'currency_format' => '$1,0.00',
                'exchange_rate' => 1.22560000,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 18,
                'currency_name' => 'Swedish Krona',
                'currency_symbol' => 'kr',
                'currency_code' => 'SEK',
                'currency_format' => 'kr1 0,00',
                'exchange_rate' => 6.45870018,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 19,
                'currency_name' => 'Swiss Franc',
                'currency_symbol' => 'CHF',
                'currency_code' => 'CHF',
                'currency_format' => 'CHF1\'0.00',
                'exchange_rate' => 0.92259997,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 20,
                'currency_name' => 'Taiwan New Dollar',
                'currency_symbol' => 'NT$',
                'currency_code' => 'TWD',
                'currency_format' => 'NT$1,0.00',
                'exchange_rate' => 28.95199966,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 21,
                'currency_name' => 'Thai Baht',
                'currency_symbol' => '฿',
                'currency_code' => 'THB',
                'currency_format' => '฿1,0.00',
                'exchange_rate' => 30.09499931,
                'active' => 0,
                'created_at' => '2013-11-29 19:51:38',
                'updated_at' => '2013-11-29 19:51:38',
            ],
            [
                'id' => 22,
                'currency_name' => 'Ukrainian hryvnia',
                'currency_symbol' => '₴',
                'currency_code' => 'UAH',
                'currency_format' => '₴1 0,00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 23,
                'currency_name' => 'Icelandic króna',
                'currency_symbol' => 'kr',
                'currency_code' => 'ISK',
                'currency_format' => 'kr1.000',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 24,
                'currency_name' => 'Croatian kuna',
                'currency_symbol' => 'kn',
                'currency_code' => 'HRK',
                'currency_format' => 'kn1.0,00', // This needs verifying
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 25,
                'currency_name' => 'Romanian leu',
                'currency_symbol' => 'lei',
                'currency_code' => 'RON',
                'currency_format' => '1.0,00lei', // This needs verifying
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 26,
                'currency_name' => 'Bulgarian lev',
                'currency_symbol' => 'лв.',
                'currency_code' => 'BGN',
                'currency_format' => 'лв1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 27,
                'currency_name' => 'Turkish lira',
                'currency_symbol' => '₺',
                'currency_code' => 'TRY',
                'currency_format' => '₺1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 28,
                'currency_name' => 'Chilean peso',
                'currency_symbol' => '$',
                'currency_code' => 'CLP',
                'currency_format' => '$1!0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 29,
                'currency_name' => 'South African rand',
                'currency_symbol' => 'R',
                'currency_code' => 'ZAR',
                'currency_format' => 'R1 0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 30,
                'currency_name' => 'Brazilian real',
                'currency_symbol' => 'R$',
                'currency_code' => 'BRL',
                'currency_format' => 'R$1.0,00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 31,
                'currency_name' => 'Malaysian ringgit',
                'currency_symbol' => 'RM',
                'currency_code' => 'MYR',
                'currency_format' => 'RM1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 32,
                'currency_name' => 'Russian ruble',
                'currency_symbol' => '₽',
                'currency_code' => 'RUB',
                'currency_format' => '₽1.0,00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 33,
                'currency_name' => 'Indonesian rupiah',
                'currency_symbol' => 'Rp',
                'currency_code' => 'IDR',
                'currency_format' => 'Rp1.0,00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 34,
                'currency_name' => 'Indian rupee',
                'currency_symbol' => '₹',
                'currency_code' => 'INR',
                'currency_format' => '₹1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 35,
                'currency_name' => 'Korean won',
                'currency_symbol' => '₩',
                'currency_code' => 'KRW',
                'currency_format' => '₩1,0',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
            [
                'id' => 36,
                'currency_name' => 'Renminbi',
                'currency_symbol' => '¥',
                'currency_code' => 'CNY',
                'currency_format' => '¥1,0.00',
                'exchange_rate' => 0.00,
                'active' => 0,
                'created_at' => '2015-07-22 23:25:30',
                'updated_at' => '2015-07-22 23:25:30',
            ],
        ];

        DB::table($this->table_name)->insert($currencies);
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
}
