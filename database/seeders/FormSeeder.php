<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class FormSeeder extends Seeder
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
            $form = new Form();
            $form->name = $faker->name;
            $form->account_name = $faker->company;
            $form->account_number = $faker->bankAccountNumber ;
            $form->branch = $faker->company;
            $form->updated_by = 1;
            $form->created_by = 1;
            $form->deleted_by = 1;
            // $form->deleted_at = $faker->dateTime($unixTimestamp);
            $form->save();
        endfor;
    }
}
