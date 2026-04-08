<?php

namespace Database\Seeders;

use App\Models\Transpfer;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class TranspferSeeder extends Seeder
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
            $transfer = new Transpfer();
            $transfer->date = $faker->date;
            $transfer->voucher_no = $faker->randomNumber;
            $transfer->from_branch_id =rand(1,10);
            $transfer->from_store_id =rand(1,10);
            $transfer->to_branch_id =rand(1,10);
            $transfer->to_store_id =rand(1,10);
            $transfer->quantity = $faker->randomNumber;
            $transfer->price = $faker->randomNumber;
            $transfer->updated_by = 1;
            $transfer->created_by = 1;
            $transfer->deleted_by = 1;
            // $transfer->deleted_at = $faker->dateTime($unixTimestamp);
            $transfer->save();
        endfor;
    }
}
