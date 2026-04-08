<?php

namespace Database\Seeders;

use App\Models\UserManage;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class UserManageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $unixTimestamp = time();
        for ($i = 0; $i < 10; $i++) :
            $userManage = new UserManage();
            $userManage->firstname = $faker->firstName;
            $userManage->lastname = $faker->lastName;
            $userManage->email = $faker->email;
            $userManage->phone = $faker->phoneNumber;
            $userManage->branch_id = rand(1, 10);
            $userManage->status_id = rand(1, 10);
            $userManage->role_id = rand(1, 10);
            $userManage->updated_by = 1;
            $userManage->created_by = 1;
            $userManage->deleted_by = 1;
            // $userManage->deleted_at = $faker->dateTime($unixTimestamp);
            $userManage->save();
        endfor;
    }
}
