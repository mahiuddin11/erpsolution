<?php
namespace Database\Seeders;

use App\Models\Bank;

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class BankSeeder extends Seeder
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
            $bank = new Bank();
            $bank->bank_name = $faker->name;
            $bank->account_name = $faker->company;
            $bank->account_number = $faker->bankAccountNumber;
            $bank->branch = $faker->name;
            $bank->updated_by = 1;
            $bank->created_by = 1;
            $bank->deleted_by = 1;
            // $bank->deleted_at = $faker->dateTime($unixTimestamp);
            $bank->save();
        endfor;
    }
}
