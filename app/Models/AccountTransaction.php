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
        'party_type',
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

    // public function resellerBill()
    // {
    //     return $this->belongsTo(BandwidthSaleInvoice::class, 'table_id', 'id');
    // }

    // public function upstreamBill()
    // {
    //     return $this->belongsTo(PurchaseBill::class, 'table_id', 'id');
    // }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    // public function resellerCustomer()
    // {
    //     return $this->belongsTo(BandwidthCustomer::class, 'customer_id', 'id');
    // }

    // public function providerCustomer()
    // {
    //     return $this->belongsTo(Provider::class, 'customer_id', 'id');
    // }

    // public function billing()
    // {
    //     return $this->belongsTo(Billing::class, 'table_id', 'id');
    // }

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

    public function transactionable()
    {
        return $this->morphTo('transactionable', 'type', 'table_id');
    }

    public function getCustomInvoiceAttribute()
    {

        if (!in_array($this->type, ['sale', 'purchase'])) {
            return 'N/A';
        }

        if (!$this->transactionable) {
            return 'N/A';
        }

        $model = $this->transactionable;

        if ($model instanceof \App\Models\Purchases) {
            return $model->custom_invoice ?? 'N/A';
        }

        if ($model instanceof \App\Models\Sale) {
            return $model->po_invoice ?? 'N/A';
        }

        return 'N/A';
    }

    /*     public function getVoucherUrlAttribute()
    {

        switch ($this->type) {
            case 'purchase':
                $purchase = $this->transactionable; // Purchases model

                if (!$purchase) {
                    return null;
                }

                if ($purchase->purchase_type === 'Manual') {
                    return route('inventorySetup.purchase.pvinvoice', $this->table_id);
                }

                return route('inventorySetup.purchase.show', $this->table_id); // Direct

            case 'sale':
                return route('sale.sale.show', $this->table_id);

            case 'debit_voucher':
                return route('settings.dabit.voucher.show', $this->table_id);

            case 'credit_voucher':
                return route('settings.credit.voucher.show', $this->table_id);

            case 'contra_voucher':
                return route('settings.contra.voucher.show', $this->table_id);

            case 'journal_voucher':

                return route('settings.journal.voucher.show', $this->table_id);

            default:
                return null;
        }
    } */

    public function getVoucherUrlAttribute()
    {
        switch ($this->type) {
            case 'purchase':
                $purchase = $this->transactionable;
                if (!$purchase) return null;

                return $purchase->purchase_type === 'Manual'
                    ? route('inventorySetup.purchase.pvinvoice', $this->table_id)
                    : route('inventorySetup.purchase.show', $this->table_id);

            case 'sale':
                return $this->transactionable ? route('sale.sale.show', $this->table_id) : null;

            case 'debit_voucher':
                return \App\Models\DabitVoucher::where('id', $this->table_id)->exists()
                    ? route('settings.dabit.voucher.show', $this->table_id) : null;

            case 'credit_voucher':
                return \App\Models\CreditVoucher::where('id', $this->table_id)->exists()
                    ? route('settings.credit.voucher.show', $this->table_id) : null;

            case 'contra_voucher':
                return \App\Models\ContraVoucher::where('id', $this->table_id)->exists()
                    ? route('settings.contra.voucher.show', $this->table_id) : null;

            case 'journal_voucher':
                return \App\Models\JournalVoucher::where('id', $this->table_id)->exists()
                    ? route('settings.journal.voucher.show', $this->table_id) : null;

            default:
                return null;
        }
    }
}
