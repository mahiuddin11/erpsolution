<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {    
         $customerType=array('Corporate','Local','Whole Salar','Others');
        $unixTimestamp =time();
        for ($i = 1; $i < 10; $i++) :
            $customer = new Customer();
            $customer->business_name ="ABC"; //$faker->company;
            $customer->customer_type = $customerType[rand(0,3)];
            $customer->branch_id = rand(1,10);
             $customer->customerCode = 'CU' . str_pad($i, 5, "0", STR_PAD_LEFT);
            $customer->name = $faker->name;
            $customer->email = $faker->email;
            $customer->phone = $faker->phoneNumber;
            $customer->address = $faker->address;
            $customer->city = rand(1,4);//$faker->city;
            $customer->state = rand(1,4);//$faker->city;
            $customer->country = rand(1,4);//$faker->country;
            $customer->pay_term = $faker->languageCode;
            $customer->pay_term_type = $faker->languageCode;
            $customer->status = 1;
            $customer->updated_by = 1;
            $customer->created_by = 1;
            $customer->deleted_by = 1;
            // $customer->deleted_at = $faker->dateTime($unixTimestamp);
            $customer->save();
        endfor;
    }
}
