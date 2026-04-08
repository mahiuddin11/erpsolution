<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class StoreSeeder extends Seeder
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
            $store = new Store();
            $store->name = $faker->name;
            $store->branch_id =rand(1,10);
            $store->email = $faker->email;
            $store->phone = $faker->PhoneNumber;
            $store->address = $faker->address;
            $store->updated_by = 1;
            $store->created_by = 1;
            $store->deleted_by = 1;
            // $store->deleted_at = $faker->dateTime($unixTimestamp);
            $store->save();
        endfor;
    }
}
