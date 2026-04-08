<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class EmployeeSeeder extends Seeder
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
            $employee = new Employee();
            $employee->branch_id =rand(1,10);
            $employee->store_id =rand(1,10);
            $employee->parent_id =rand(1,10);
            $employee->designation = $faker->userName;
            $employee->name = $faker->name;
            $employee->email = $faker->email;
            $employee->phone = $faker->phoneNumber;
            $employee->address = $faker->address;
            $employee->updated_by = 1;
            $employee->created_by = 1;
            $employee->deleted_by = 1;
            // $employee->deleted_at = $faker->dateTime($unixTimestamp);
            $employee->save();
        endfor;
    }
}