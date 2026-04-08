<?php

namespace Database\Seeders;
use App\Models\Currency;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $unixTimestamp =time();
        for ($i = 0; $i < 10; $i++) :
            $currency = new Currency();
            $currency->currency_name = $faker->languageCode;
            $currency->currency_symbol = $faker->currencyCode;
            $currency->exchange_rate = $faker->randomDigit;
            $currency->updated_by = 1;
            $currency->created_by = 1;
            $currency->deleted_by = 1;
            // $currency->deleted_at = $faker->dateTime($unixTimestamp);
            $currency->save();
        endfor;
    }
}
