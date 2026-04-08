<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Adjust extends Model
{
    use HasFactory;
    use SoftDeletes;

   public function account()
   {
       return $this->belongsTo(Accounts::class ,'account_id' ,'id');
   }
   public function customer()
   {
       return $this->belongsTo(Customer::class ,'customer_id' ,'id');
   }
   public function branch()
   {
       return $this->belongsTo(Branch::class ,'branch_id' ,'id');
   }

}
