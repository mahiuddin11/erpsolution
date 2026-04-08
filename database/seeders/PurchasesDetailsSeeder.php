<?php

namespace Database\Seeders;

use App\Models\PurchasesDetails;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class PurchasesDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $unixTimestamp = time();
        for ($i = 0; $i < 10; $i++) :
            $purchaseDetails = new PurchasesDetails();
            $purchaseDetails->date = $faker->date;
            $purchaseDetails->purchases_id = rand(1, 10);
            $purchaseDetails->branch_id = rand(1, 10);
            // $purchaseDetails->store_id =rand(1,10);
            $purchaseDetails->product_id = rand(1, 10);
            $purchaseDetails->unit_pirce = $faker->randomNumber;
            $purchaseDetails->total_price = $faker->randomNumber;
            $purchaseDetails->updated_by = 1;
            $purchaseDetails->created_by = 1;
            $purchaseDetails->deleted_by = 1;
            // $purchaseDetails->deleted_at = $faker->dateTime($unixTimestamp);
            $purchaseDetails->save();
        endfor;
    }
}
