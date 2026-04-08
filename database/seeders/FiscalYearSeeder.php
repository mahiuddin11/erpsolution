<?php

namespace Database\Seeders;
use App\Models\FiscalYear;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class FiscalYearSeeder extends Seeder
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
            $fiscicalYear = new FiscalYear();
            $fiscicalYear->branch_id =rand(1,10);
            $fiscicalYear->date = $faker->date;
            $fiscicalYear->fiscal_year = $faker->date;
            $fiscicalYear->updated_by = 1;
            $fiscicalYear->created_by = 1;
            $fiscicalYear->deleted_by = 1;
            // $fiscicalYear->deleted_at = $faker->dateTime($unixTimestamp);
            $fiscicalYear->save();
        endfor;
    }
}

