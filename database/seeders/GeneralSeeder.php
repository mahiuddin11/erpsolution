<?php

namespace Database\Seeders;

use App\Models\General;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class GeneralSeeder extends Seeder
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
            $general = new General();
            $general->invoice_no =rand(1,10);
            $general->branch_id =rand(1,10);
            $general->store_id =rand(1,10);
            $general->form_id =rand(1,10);
            $general->date = $faker->date;
            $general->debit = $faker->creditCardNumber;
            $general->credit = $faker->creditCardNumber;
            $general->note = $faker->text;

            $general->updated_by = 1;
            $general->created_by = 1;
            $general->deleted_by = 1;
            // $general->deleted_at = $faker->dateTime($unixTimestamp);
            $general->save();
        endfor;
    }
}
