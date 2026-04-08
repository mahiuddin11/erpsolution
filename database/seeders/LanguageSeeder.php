<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class LanguageSeeder extends Seeder
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
            $language = new Language();
            $language->name = $faker->name;
            $language->flug = $faker->name;
            $language->updated_by = 1;
            $language->created_by = 1;
            $language->deleted_by = 1;
            // $language->deleted_at = $faker->dateTime($unixTimestamp);
            $language->save();
        endfor;
    }
}
