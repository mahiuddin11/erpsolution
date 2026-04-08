<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $unixTimestamp = time();
        for ($i = 1; $i < 10; $i++) :
            $product = new Product();
            $product->name = $faker->name;
            $product->productCode = 'PR' . str_pad($i, 5, "0", STR_PAD_LEFT);
            $product->category_id = rand(1, 10);
            $product->brand_id = rand(1, 10);
            $product->unit_id = rand(1, 10);
            $product->purchases_price = $faker->numberBetween(30, 40);
            $product->sale_price = $faker->numberBetween(50, 60);
            $product->low_stock = $faker->randomNumber;
            $product->updated_by = 1;
            $product->created_by = 1;
            $product->deleted_by = 1;
            // $product->deleted_at = $faker->dateTime($unixTimestamp);
            $product->save();
        endfor;
    }
}
