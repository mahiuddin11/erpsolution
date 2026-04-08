<?php

namespace Database\Seeders;

use App\Models\Opening;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class OpeningSeeder extends Seeder
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
            $opening = new Opening();
            $opening->branch_id = rand(1, 10);
            // $opening->store_id =rand(1,10);
            $opening->account_id = rand(1, 10);
            $opening->date = $faker->date;
            $opening->debit = $faker->creditCardNumber;
            $opening->credit = $faker->creditCardNumber;
            $opening->updated_by = 1;
            $opening->created_by = 1;
            $opening->deleted_by = 1;
            // $opening->deleted_at = $faker->dateTime($unixTimestamp);
            $opening->save();
        endfor;
    }
}
