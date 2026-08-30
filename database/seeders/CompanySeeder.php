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
        $companySetUp->company_name = 'WTBL';
        $companySetUp->website = 'http://wtbl.com.bd/';
        $companySetUp->phone = '01785654241';
        $companySetUp->email = 'info@wtbl.com.bd';
        $companySetUp->address = 'House-1248(5nd floor), Road-9, Mirpur Dohs, Bangladesh';
        $companySetUp->task_identification_number = '000000';
        $companySetUp->updated_by = 1;
        $companySetUp->created_by = 1;
        $companySetUp->deleted_by = 1;
        $companySetUp->save();
    }
}
