<?php

namespace Database\Seeders;

use App\Models\GeneralSetup;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class GeneralSetupSeeder extends Seeder
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
            $generalSetUp = new GeneralSetup();
            $generalSetUp->currency = $faker->currencyCode;
            $generalSetUp->currency_position = $faker->languageCode;
            $generalSetUp->language = $faker->languageCode ;
            $generalSetUp->timezone = $faker->time;
            $generalSetUp->dateformat = $faker->date;
            $generalSetUp->decimal_separate = $faker->phoneNumber;
            $generalSetUp->thousand_separate = $faker->phoneNumber;

            $generalSetUp->updated_by = 1;
            $generalSetUp->created_by = 1;
            $generalSetUp->deleted_by = 1;
            // $generalSetUp->deleted_at = $faker->dateTime($unixTimestamp);
            $generalSetUp->save();
        endfor;
    }
}
