<?php
/*   public function prstore($request)
    {

        DB::beginTransaction();

        try {
            $request->branch_id = $request->sub_warehouse_id ?? $request->branch_id;
            $purchase = new $this->purchases();
            $purchase->invoice_no = $request->invoice_no;
            $purchase->date = $request->date;
            $purchase->purchase_order_id = $request->purchase_order_id;
            $purchase->type =  'Project';
            $purchase->branch_id =  0;
            $purchase->project_id = $request->project_id;
            $purchase->supplier_id = $request->supplier_id ?? 0;
            $purchase->quantity = array_sum($request->qty);
            $purchase->purchase_type = 'Manual';
            $purchase->subtotal = array_sum($request->unitprice);
            $purchase->grand_total = array_sum($request->total);
            $purchase->status = 'Pending';
            $purchase->payment_type = $request->payment_type;
            $purchase->discount = $request->discount;
            $purchase->paid_amount = $request->paid_amount + $request->advance_payment; // payment and advance pay addition
            $purchase->due_amount = $request->cart_due;
            $purchase->created_by = Auth::user()->id;
            $purchase->narration = $request->narration;


            if ($request->has('chart_of_account_id')) {
                $purchase->chart_of_account_id = $request->chart_of_account_id;
            }
            if ($request->has('account_number')) {
                $purchase->account_number = $request->account_number;
            }
            if ($request->has('check_number')) {
                $purchase->check_number = $request->check_number;
            }
            if ($request->has('bank')) {
                $purchase->bank = $request->bank;
            }
            if ($request->has('bank_branch')) {
                $purchase->bank_branch = $request->bank_branch;
            }
            if ($request->has('input_net_total')) {
                $purchase->net_total = $request->input_net_total;
            }
            $purchase->save();
            $purchases_id = $purchase->id;

            $category_id = $request->category_nm;
            $supplier_id = $request->supplier_nm;
            $proName = $request->product_nm;
            $subtotal = $request->unitprice;
            $grand_total = $request->total;
            $qty = $request->qty;



            for ($i = 0; $i < count($supplier_id); $i++) {
                $purchaseDetail = new PurchasesDetails();
                $purchaseDetail->product_id = $proName[$i];
                $purchaseDetail->supplier_id = $supplier_id[$i];
                $purchaseDetail->category_id = $category_id[$i];
                $purchaseDetail->purchasetype = $request->purchasetype[$i];
                $purchaseDetail->quantity = $qty[$i];
                $purchaseDetail->project_id = $request->project_id;
                $purchaseDetail->unit_price = $subtotal[$i];
                $purchaseDetail->total_price = $grand_total[$i];
                $purchaseDetail->purchases_id = $purchases_id;
                $purchaseDetail->date = $request->date;
                $purchaseDetail->created_by = Auth::user()->id;
                $purchaseDetail->save();
            }

            $purchaseorder['approved_by'] = Auth::user()->id;
            $purchaseorder['approved_at'] = date('Y-m-d');
            $purchaseorder['status'] = 'Complete';
            PurchaseOrder::where('id', $request->purchase_order_id)->update($purchaseorder);

            // $invoice = AccountTransaction::accountInvoice();

            $invoice = (new AccountTransaction())->accountInvoice();

            // old function
            foreach ($supplier_id as $key => $id) {

                $supplier = Supplier::find($id);
                $debit = AccountTransaction::where([
                    'payment_invoice' => $request->invoice_no,
                    'invoice'       => $invoice,
                    'table_id'      => $purchases_id,
                    'account_id'    => getAccountByUniqueID(22)->id, //purchase
                    'project_id'    => $request->project_id,
                    'supplier_id'   => $id
                ])->first();

                $debitAmnt = isset($debit->debit) ? $debit->debit : 0;
                $totalDebit = $debitAmnt + $request->total[$key];


                AccountTransaction::updateOrCreate([
                    'payment_invoice' => $request->invoice_no,
                    'invoice'       => $invoice,
                    'table_id'      => $purchases_id,
                    'account_id'    => getAccountByUniqueID(22)->id,
                    'project_id'    => $request->project_id,
                    'supplier_id'   => $id
                ], [
                    'type' => 1,
                    'branch_id' => $request->branch_id ?? 0,
                    'debit' =>  $totalDebit,
                    'remark' => $request->narration,
                    'created_at' => $request->date,
                    'created_by' => Auth::id(),
                ]);
                $credit = AccountTransaction::where([
                    'payment_invoice' => $request->invoice_no,
                    'invoice'       => $invoice,
                    'table_id'      => $purchases_id,
                    'account_id'    => $supplier->account->id,
                    'project_id'    => $request->project_id,
                    'supplier_id'   => $id
                ])->first();

                $credit = isset($credit->credit) ? $credit->credit : 0;
                $totalCredit = $credit + $request->total[$key];


                AccountTransaction::updateOrCreate(
                    [
                        'payment_invoice' => $request->invoice_no,
                        'invoice'       => $invoice,
                        'table_id'      => $purchases_id,
                        'account_id'    => $supplier->account->id,
                        'project_id'    => $request->project_id,
                        'supplier_id'   => $id
                    ],
                    [
                        'type' => 1,
                        'branch_id' => $request->branch_id ?? 0,
                        'credit' =>  $totalCredit,
                        'created_at' => $request->date,
                        'remark' => $request->narration,
                        'created_by' => Auth::id(),
                    ]
                );
            }

            if ($request->payment_type == 'cash') {
                $transection = new Transection();
                $transection->date = $request->date;
                $transection->account_id = $request->chart_of_account_id;
                $transection->payment_id = $purchases_id;
                $transection->branch_id = $request->supplier_id;
                $transection->type =  11;
                $transection->note = $request->note;
                $transection->amount =  array_sum($request->total) - $request->discount;
                $transection->credit = array_sum($request->total) - $request->discount;
                $transection->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e->getMessage());
            redirect('inventory-purchase-create')->with('error', 'Something Wrong Please try again');
        }
        return $purchase;
    }
 */

    public function prstore($request)
    {
        DB::beginTransaction();

        try {

            $request->branch_id = $request->sub_warehouse_id ?? $request->branch_id;

            // =========================
            // 1. PURCHASE HEADER
            // =========================
            $purchase = new $this->purchases();
            $purchase->invoice_no       = $request->invoice_no;
            $purchase->date             = $request->date;
            $purchase->purchase_order_id = $request->purchase_order_id;
            $purchase->type             = 'Project';
            $purchase->branch_id        = 0;
            $purchase->project_id       = $request->project_id;
            $purchase->supplier_id      = $request->supplier_id ?? 0;
            $purchase->quantity         = array_sum($request->qty);
            $purchase->purchase_type    = 'Manual';
            $purchase->subtotal         = array_sum($request->unitprice);
            $purchase->grand_total      = array_sum($request->total);
            $purchase->status           = 'Pending';
            $purchase->payment_type     = $request->payment_type;
            $purchase->discount         = $request->discount;
            $purchase->paid_amount      = ($request->paid_amount ?? 0) + ($request->advance_payment ?? 0);
            $purchase->due_amount       = $request->cart_due;
            $purchase->created_by       = Auth::id();
            $purchase->narration        = $request->narration;

            // Optional fields (old system compatible)
            if ($request->has('chart_of_account_id')) {
                $purchase->chart_of_account_id = $request->chart_of_account_id;
            }
            if ($request->has('account_number')) {
                $purchase->account_number = $request->account_number;
            }
            if ($request->has('check_number')) {
                $purchase->check_number = $request->check_number;
            }
            if ($request->has('bank')) {
                $purchase->bank = $request->bank;
            }
            if ($request->has('bank_branch')) {
                $purchase->bank_branch = $request->bank_branch;
            }
            if ($request->has('input_net_total')) {
                $purchase->net_total = $request->input_net_total;
            }

            $purchase->save();
            $purchaseId = $purchase->id;

            // =========================
            // 2. PURCHASE DETAILS + PARTY GROUPING
            // =========================
            $category_id = $request->category_nm;
            $supplier_nm = $request->supplier_nm ?? [];
            $ledger_nm   = $request->ledger_nm   ?? [];
            $products    = $request->product_nm;
            $qty         = $request->qty;
            $unitprice   = $request->unitprice;
            $total       = $request->total;

            $partyTotals = [];
            
            
            
            for ($i = 0; $i < count($products); $i++) {

                $supplierId = $supplier_nm[$i] ?? null;
                $ledgerId   = $ledger_nm[$i]   ?? null;
                $amount     = $total[$i];

                // --- Purchase Detail row ---
                $purchaseDetail                = new PurchasesDetails();
                $purchaseDetail->product_id    = $products[$i];
                $purchaseDetail->category_id   = $category_id[$i];
                $purchaseDetail->project_id    = $request->project_id;
                $purchaseDetail->quantity      = $qty[$i];
                $purchaseDetail->unit_price    = $unitprice[$i];
                $purchaseDetail->total_price   = $amount;
                $purchaseDetail->purchases_id  = $purchaseId;
                $purchaseDetail->date          = $request->date;
                $purchaseDetail->created_by    = Auth::id();

                
                $purchaseDetail->supplier_id   = !empty($supplierId) ? $supplierId : 0;
                $purchaseDetail->ledger_id     = !empty($ledgerId)   ? $ledgerId   : 0;

               
                // purchasetype থাকলে সেট করুন (old system)
                if (isset($request->purchasetype[$i])) {
                    $purchaseDetail->purchasetype = $request->purchasetype[$i];
                }

                $purchaseDetail->save();

            
                if (!empty($supplierId)) {

                    // Supplier থেকে purchase
                    $supplier  = Supplier::find($supplierId);
                    $accountId = $supplier->account_id ?? ($supplier->account->id ?? 0);

                    if ($accountId) {
                        $key = 'supplier_' . $supplierId;

                        if (isset($partyTotals[$key])) {
                            $partyTotals[$key]['amount'] += $amount;
                        } else {
                            $partyTotals[$key] = [
                                'account_id'  => $accountId,
                                'supplier_id' => $supplierId,
                                'ledger_id'   => null,
                                'amount'      => $amount,
                            ];
                        }
                    }
                } elseif (!empty($ledgerId)) {

                    // Customer / Ledger party থেকে purchase
                    // ledger_id সরাসরি chart_of_account id হিসেবে ব্যবহার হচ্ছে
                    $key = 'ledger_' . $ledgerId;

                    if (isset($partyTotals[$key])) {
                        $partyTotals[$key]['amount'] += $amount;
                    } else {
                        $partyTotals[$key] = [
                            'account_id'  => $ledgerId,
                            'supplier_id' => null,
                            'ledger_id'   => $ledgerId,
                            'amount'      => $amount,
                        ];
                    }
                }
            }

            // =========================
            // 3. PURCHASE ORDER UPDATE
            // =========================
            if ($request->purchase_order_id) {
                PurchaseOrder::where('id', $request->purchase_order_id)->update([
                    'approved_by' => Auth::id(),
                    'approved_at' => now()->toDateString(),
                    'status'      => 'Complete',
                ]);
            }

            

            
            foreach ($partyTotals as $key => $party) {
                
                if (empty($party['amount']) || $party['amount'] <= 0) continue;

                $ledger = ChartOfAccount::find($party['account_id']);

                if (!$ledger) continue;

                $supId = null;
                $cusId = null;

                switch ($ledger->accountable_type) {
                    case 'App\Models\Supplier':
                        $supId = $ledger->accountable_id;
                        break;

                    case 'App\Models\Customer':
                        $cusId = $ledger->accountable_id;
                        break;
                }

                $invoice = (new AccountTransaction())->accountInvoice();
                
          
                // ---- DEBIT: Purchase A/C (party-tagged) ----
                $debit = AccountTransaction::where([
                    'payment_invoice' => $request->invoice_no,
                    'invoice'       => $invoice,
                    'table_id'      => $purchaseId,
                    'account_id'    => getAccountByUniqueID(22)->id, //purchase
                    'project_id'    => $request->project_id,
                    'supplier_id'   => $supId ?? '',
                    'customer_id'   => $cusId ?? '' 
                ])->first();

                $debitAmnt = isset($debit->debit) ? $debit->debit : 0;
                $totalDebit = $debitAmnt + $party['amount'];

                
                AccountTransaction::updateOrCreate([
                    'payment_invoice' => $request->invoice_no,
                    'invoice'       => $invoice,
                    'table_id'      => $purchaseId,
                    'account_id'    => getAccountByUniqueID(22)->id,
                    'project_id'    => $request->project_id,
                    'supplier_id'   => $supId ?? '',
                    'customer_id'   => $cusId ?? ''
                ],
                [
                    'type' => 1,
                    'branch_id' => $request->branch_id ?? 0,
                    'debit' =>  $totalDebit,
                    'remark' => $request->narration,
                    'created_at' => $request->date,
                    'created_by' => Auth::id(),
                ]);
                           

                // ---- CREDIT: Party A/C (supplier / ledger) ----
                $credit = AccountTransaction::where([
                    'payment_invoice' => $request->invoice_no,
                    'invoice'       => $invoice,
                    'table_id'      => $purchaseId,
                    'account_id'    => $party['account_id'],
                    'project_id'    => $request->project_id,
                    'supplier_id'   => $supId ?? '',
                    'customer_id'   => $cusId ?? ''
                ])->first();

                $credit = isset($credit->credit) ? $credit->credit : 0;
                $totalCredit = $credit + $party['amount'];

                AccountTransaction::updateOrCreate(
                    [
                        'payment_invoice' => $request->invoice_no,
                        'invoice'       => $invoice,
                        'table_id'      => $purchaseId,
                        'account_id'    => $party['account_id'],
                        'project_id'    => $request->project_id,
                        'supplier_id'   => $supId ?? '',
                        'customer_id'   => $cusId ?? ''
                    ],
                    [
                        'type' => 1,
                        'branch_id' => $request->branch_id ?? 0,
                        'credit' =>  $totalCredit,
                        'created_at' => $request->date,
                        'remark' => $request->narration,
                        'created_by' => Auth::id(),
                    ]
                );
            }



            // =========================
            // 6. CASH PAYMENT TRANSACTION (old system compatible)
            // =========================
            if ($request->payment_type == 'cash') {

                $transection             = new Transection();
                $transection->date       = $request->date;
                $transection->account_id = $request->chart_of_account_id;
                $transection->payment_id = $purchaseId;
                $transection->branch_id  = $request->supplier_id ?? 0;
                $transection->type       = 11;
                $transection->note       = $request->note ?? $request->narration;
                $transection->amount     = array_sum($request->total) - ($request->discount ?? 0);
                $transection->credit     = array_sum($request->total) - ($request->discount ?? 0);
                $transection->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            
            dd($e->getMessage(), $e->getLine(), $e->getFile());
         
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }

        return $purchase;
    }