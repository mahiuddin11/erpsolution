<?php

namespace Database\Seeders;

use App\Models\Inovice;
use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
class InoviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {    
         $invoiceType=array('Corporate','Local','Whole Salar','Others');
        $unixTimestamp =time();
        for ($i = 0; $i < 10; $i++) :
            $invoice = new Inovice();
            $invoice->date =$faker->date;
            $invoice->customer_id = rand(1,10);
            $invoice->branch_id =  rand(1,10);
            $invoice->store_id =  rand(1,10);
            $invoice->invoice_no = rand(1,4);//$faker->city;
            $invoice->payment_type = "CASH";
            $invoice->subtotal = $faker->randomFloat;
            $invoice->discount = $faker->randomFloat;
            $invoice->grand_total =  $faker->randomFloat;
            $invoice->loder = rand(1,4);
            $invoice->transportation = rand(1,4);//$faker->city;
            $invoice->paid_amount = rand(1,4);//$faker->country;
            $invoice->dur_amount =$faker->randomFloat;
            $invoice->status = 1;
            $invoice->updated_by = 1;
            $invoice->created_by = 1;
            $invoice->deleted_by = 1;
            // $invoice->deleted_at = $faker->dateTime($unixTimestamp);
            $invoice->save();
        endfor;
    }
}
