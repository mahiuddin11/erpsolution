<?php

namespace Database\Seeders;

use App\Models\Purchases;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class PurchasesSeeder extends Seeder
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
            $purches = new Purchases();
            $purches->date =$faker->date;
            $purches->supplier_id = rand(1,10);
            $purches->branch_id =  rand(1,10);
            $purches->store_id =  rand(1,10);
            $purches->invoice_no = rand(1,4);//$faker->city;
            $purches->payment_type = "CASH";
            $purches->subtotal = $faker->randomFloat;
            $purches->discount = $faker->randomFloat;
            $purches->grand_total =  $faker->randomFloat;
            $purches->loder = rand(1,4);
            $purches->transportation = rand(1,4);//$faker->city;
            $purches->paid_amount = rand(1,4);//$faker->country;
            $purches->dur_amount =$faker->randomFloat;
            $purches->status = 1;
            $purches->updated_by = 1;
            $purches->created_by = 1;
            $purches->deleted_by = 1;
            // $purches->deleted_at = $faker->dateTime($unixTimestamp);
            $purches->save();
        endfor;
    }
}
