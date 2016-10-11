<?php

use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $currencies = include('./vendor/torann/currency/resources/currencies.php');
        $created = new DateTime('now');
        foreach ($currencies as $code => $details) {
            DB::table('currencies')
              ->insert(array(
                  array(
                      'name'          => $details['name'],
                      'code'          => $code,
                      "symbol"        => $details['symbol'],
                      "format"        => $details['format'],
                      "exchange_rate" => $details['exchange_rate'],
                      "created_at"    => $created,
                      "updated_at"    => $created,
                  ),
              ));
        }
        Artisan::call('currency:update');
    }

    public function down()
    {
        DB::table('currencies')
          ->truncate();
    }
}
