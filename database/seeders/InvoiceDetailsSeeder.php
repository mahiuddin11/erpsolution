<?php

namespace Database\Seeders;

use App\Models\InvoiceDetails;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class InvoiceDetailsSeeder extends Seeder
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
            $invoiceDetails = new InvoiceDetails();
            $invoiceDetails->invoice_id =rand(1,10);
            $invoiceDetails->branch_id =rand(1,10);
            $invoiceDetails->store_id =rand(1,10);
            $invoiceDetails->product_id =rand(1,10);
            $invoiceDetails->date = $faker->date;
            $invoiceDetails->unit_pirce = $faker->randomNumber;
            $invoiceDetails->total_price = $faker->randomNumber;
            $invoiceDetails->updated_by = 1;
            $invoiceDetails->created_by = 1;
            $invoiceDetails->deleted_by = 1;
            // $invoiceDetails->deleted_at = $faker->dateTime($unixTimestamp);
            $invoiceDetails->save();
        endfor;
    }
}
