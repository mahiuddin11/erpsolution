<?php

namespace Database\Seeders;

use App\Models\ProductUnit;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ProductUnitSeeder extends Seeder
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
            $productUnit = new ProductUnit();
            $productUnit->name = $faker->name;
            $productUnit->updated_by = 1;
            $productUnit->created_by = 1;
            $productUnit->deleted_by = 1;
            // $productUnit->deleted_at = $faker->dateTime($unixTimestamp);
            $productUnit->save();
        endfor;
    }
}
