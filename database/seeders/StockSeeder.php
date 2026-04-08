<?php

namespace Database\Seeders;

use App\Models\Stock;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class StockSeeder extends Seeder
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
            $stock = new Stock();
            $stock->date = $faker->date;
            $stock->general_id =rand(1,10);
            $stock->branch_id =rand(1,10);
            $stock->store_id =rand(1,10);
            $stock->product_id =rand(1,10);
            $stock->unit_price = $faker->randomNumber;
            $stock->total_price = $faker->randomNumber;
            $stock->updated_by = 1;
            $stock->created_by = 1;
            $stock->deleted_by = 1;
            // $stock->deleted_at = $faker->dateTime($unixTimestamp);
            $stock->save();
        endfor;
    }
}
