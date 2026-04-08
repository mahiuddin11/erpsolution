<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucher extends Model
{
    use HasFactory;

    function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }
    function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
    function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    function details()
    {
        return $this->hasMany(JournalVoucherDetails::class, 'journal_voucher_id');
    }
}
