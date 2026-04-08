<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $unixTimestamp = time();
        //  for ($i = 0; $i < 10; $i++) :
        $companySetUp = new Company();
        $companySetUp->logo = 'logo.png';
        $companySetUp->favicon = '';
        $companySetUp->invoice_logo = 'logo.png';
        $companySetUp->company_name = 'Xtreem Erp';
        $companySetUp->website = 'http://itwaybd.com/';
        $companySetUp->phone = '01854125454';
        $companySetUp->email = 'info@itwaybd.com';
        $companySetUp->address = 'House-1(2nd floor), Road-1, Sector-05, Uttara, Dhaka-1230, Bangladesh';
        $companySetUp->task_identification_number = '000000';
        $companySetUp->updated_by = 1;
        $companySetUp->created_by = 1;
        $companySetUp->deleted_by = 1;
        $companySetUp->save();
    }
}
