<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $unixTimestamp = '1461067200';
        for ($i = 0; $i < 1; $i++) :
            $branch = new Branch();
            $branch->name = $faker->userName;
            $branch->branchCode = 'BR' . str_pad($i, 5, "0", STR_PAD_LEFT);
            $branch->email = $faker->email;
            $branch->phone = $faker->phoneNumber;
            $branch->address = $faker->address;
            $branch->updated_by = 1;
            $branch->created_by = 1;
            $branch->deleted_by = 1;
            // $branch->deleted_at = $faker->dateTime($unixTimestamp);
            $branch->save();
        endfor;
    }
}
