<?php

namespace Database\Seeders;

use App\Models\AllVoucher;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class AllVoucherSeeder extends Seeder
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
            $allvoucher = new AllVoucher();
            $allvoucher->date = $faker->date;
            $allvoucher->form_id = rand(1,10);
            $allvoucher->voucher_no = $faker->phoneNumber;
            $allvoucher->branch_id = rand(1,10);
            $allvoucher->store_id = rand(1,10);
            $allvoucher->debit = $faker->creditCardNumber;
            $allvoucher->credit = $faker->creditCardNumber;
            $allvoucher->status_id = rand(1,10);

            $allvoucher->updated_by = 1;
            $allvoucher->created_by = 1;
            $allvoucher->deleted_by = 1;
            // $allvoucher->deleted_at = $faker->dateTime($unixTimestamp);
            $allvoucher->save();
        endfor;
    }
}
