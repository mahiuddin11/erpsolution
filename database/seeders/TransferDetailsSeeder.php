<?php

namespace Database\Seeders;


use App\Models\TransferDetails;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class TransferDetailsSeeder extends Seeder
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
            $transferDetails = new TransferDetails();
            $transferDetails->transfer_id =rand(1,10);
            $transferDetails->branch_id =rand(1,10);
            $transferDetails->store_id =rand(1,10);
            $transferDetails->product_id =rand(1,10);
            $transferDetails->date = $faker->date;
            $transferDetails->unit_pirce = $faker->randomNumber;
            $transferDetails->total_price = $faker->randomNumber;
            $transferDetails->updated_by = 1;
            $transferDetails->created_by = 1;
            $transferDetails->deleted_by = 1;
            // $transferDetails->deleted_at = $faker->dateTime($unixTimestamp);
            $transferDetails->save();
        endfor;
    }
}
