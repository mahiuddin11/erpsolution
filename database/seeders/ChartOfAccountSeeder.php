<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        // ChartOfAccount::query()->truncate();
        $account = ['Account Receivable', 'Account Payable', 'Salary', 'Cash', 'Bank', 'Mobile Banking'];
        for ($i = 0; $i < count($account); $i++) :
            $chartOfaccount = new  ChartOfAccount();
            $chartOfaccount->account_code = $faker->bankRoutingNumber;
            $chartOfaccount->accountCode =  'CA' . str_pad($i, 5, "0", STR_PAD_LEFT);
            $chartOfaccount->account_name = $account[$i];
            $chartOfaccount->branch_id = 0;
            $chartOfaccount->updated_by = 1;
            $chartOfaccount->created_by = 1;
            $chartOfaccount->deleted_by = 1;
            $chartOfaccount->save();
        endfor;
    }
}
