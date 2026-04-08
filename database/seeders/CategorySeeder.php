<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;

class CategorySeeder extends Seeder
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
            $category = new  Category();
            $category->name = $faker->name;
            $category->parent_id = rand(1,10);
            $category->updated_by = 1;
            $category->created_by = 1;
            $category->deleted_by = 1;
            // $category->deleted_at = $faker->dateTime($unixTimestamp);
            $category->save();
        endfor;
    }
}
