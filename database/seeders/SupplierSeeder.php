<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $supplierType = array('Corporate', 'Local', 'Hole Salar', 'Others');
        $unixTimestamp = time();
        for ($i = 1; $i < 10; $i++) :
            $supplier = new Supplier();
            $supplier->supplierCode = 'SP' . str_pad($i, 5, "0", STR_PAD_LEFT);
            // $supplier->branch_id =  1;
            $supplier->name = $faker->name;
            $supplier->email = $faker->email;
            $supplier->phone = $faker->phoneNumber;
            $supplier->address = $faker->address;
            // $supplier->city =   rand(1, 4);
            // $supplier->state = rand(1, 4);
            // $supplier->country = rand(1, 4); //$faker->city;
            // $supplier->pay_term = "CASH"; //$faker->country;
            // $supplier->pay_term_type = "CASH";
            $supplier->status = 'Active';
            $supplier->updated_by = 1;
            $supplier->created_by = 1;
            $supplier->deleted_by = 1;
            // $supplier->deleted_at = $faker->dateTime($unixTimestamp);
            $supplier->save();
        endfor;
    }
}
