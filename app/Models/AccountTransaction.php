<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'account_id',
        'type',
        'debit',
        'branch_id',
        'credit',
        'remark',
        'table_id',
        'created_by',
        'supplier_id',
        'customer_id',
        'employee_id',
        'project_id',
        'created_at',
        'payment_invoice',
    ];

    public function accountInvoice()
    {
        $account = AccountTransaction::latest('id')->pluck('id')->first() ?? "0";
        $invoice_no =  str_pad($account + 1, 5, "0", STR_PAD_LEFT);
        return $invoice_no;
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    public function resellerBill()
    {
        return $this->belongsTo(BandwidthSaleInvoice::class, 'table_id', 'id');
    }

    public function upstreamBill()
    {
        return $this->belongsTo(PurchaseBill::class, 'table_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function resellerCustomer()
    {
        return $this->belongsTo(BandwidthCustomer::class, 'customer_id', 'id');
    }

    public function providerCustomer()
    {
        return $this->belongsTo(Provider::class, 'customer_id', 'id');
    }

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'table_id', 'id');
    }

    /**
     * Relation with Debit Voucher
     */
    public function debitVoucher()
    {
        return $this->belongsTo(DabitVoucher::class, 'table_id', 'id');
    }

    /**
     * Relation with Credit Voucher
     */
    public function creditVoucher()
    {
        return $this->belongsTo(CreditVoucher::class, 'table_id', 'id');
    }

    public function contraVoucher()
    {
        return $this->belongsTo(ContraVoucher::class, 'table_id', 'id');
    }
    public function journalVoucher()
    {
        return $this->belongsTo(JournalVoucher::class, 'table_id', 'id');
    }
}
