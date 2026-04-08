<?php

namespace Database\Seeders;

use App\Models\GeneralLedger;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class GeneralLedgerSeeder extends Seeder
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
            $generalLedger = new GeneralLedger();
            $generalLedger->general_id =rand(1,10);
            $generalLedger->form_id =rand(1,10);
            $generalLedger->chart_id =rand(1,10);
            $generalLedger->date = $faker->date;
            $generalLedger->debit = $faker->creditCardNumber;
            $generalLedger->credit = $faker->creditCardNumber;
            $generalLedger->memo = $faker->text;

            $generalLedger->updated_by = 1;
            $generalLedger->created_by = 1;
            $generalLedger->deleted_by = 1;
            // $generalLedger->deleted_at = $faker->dateTime($unixTimestamp);
            $generalLedger->save();
        endfor;
    }
}
