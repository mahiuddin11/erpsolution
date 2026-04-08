<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $unixTimestamp = time();

        $brand = new Brand();
        $brand->name = 'Untitled';
        $brand->updated_by = 1;
        $brand->created_by = 1;
        $brand->deleted_by = 1;
        $brand->save();
    }
}
