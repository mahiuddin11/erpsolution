<?php

if (empty($request->input('search.value'))) {

    $dabitvoucher = $this->dabitVoucher::offset($start);
    // if ($auth->branch_id !== null) {
    //     $dabitvoucher = $dabitvoucher->where('branch_id', $auth->branch_id);
    // }
    $dabitvoucher = $dabitvoucher->limit($limit)
        ->orderBy($order, $dir)
        ->get();
    $totalFiltered = $this->dabitVoucher::count();
} else {

    $search = $request->input('search.value');


    $dabitvoucher = $this->dabitVoucher
        ->where(function ($query) use ($search) {

            $query->where('voucher_no', 'like', "%{$search}%")
                ->orWhere('date', 'like', "%{$search}%");

            // user name (approved_by)
            $query->orWhereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });

            // project name
            $query->orWhereHas('project', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        });

    $dabitvoucher = $dabitvoucher->offset($start)
        ->limit($limit)
        ->orderBy($order, $dir)
        ->get();

    $totalFiltered = $this->dabitVoucher::count();
}

     $search = $request->input('search.value');
        $query = $this->dabitVoucher
            ->select('id', 'voucher_no', 'date', 'approved_by', 'project_id', 'updated_by' ,'note')
            ->with([
                'user:id,name',
                'project:id,name',
                'updatedBy:id,name'
            ]);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {

                $q->where('voucher_no', 'like', "{$search}%")
                    ->orWhere('date', 'like', "{$search}%")

                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "{$search}%");
                    })

                    ->orWhereHas('project', function ($q2) use ($search) {
                        $q2->where('name', 'like', "{$search}%");
                    });
            });
        }

        $totalData = $this->dabitVoucher->count();
        $totalFiltered = (clone $query)->count();

        $dabitvoucher = $query
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

$search = $request->input('search.value');
$query = $this->dabitVoucher
    ->select('id', 'voucher_no',  'date', 'approved_by', 'project_id', 'updated_by', 'note')
    ->with([
        'user:id,name',
        'project:id,name',
        'updatedBy:id,name',
        // 'details:id,dabit_voucher_id,debit'
    ])
    ->withSum('details as total_amount', 'debit');

if (!empty($search)) {
    $query->where(function ($q) use ($search) {

        $q->where('voucher_no', 'like', "%{$search}%")
            ->orWhere('date', 'like', "%{$search}%")
            ->orWhere('note', 'like', "%{$search}%")

            ->orWhereHas('user', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })

            ->orWhereHas('project', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            });
    });
}

$totalData = $this->dabitVoucher->count();
$totalFiltered = $query->toBase()->getCountForPagination();

$dabitvoucher = $query
    ->offset($start)
    ->limit($limit)
    ->orderBy($order, $dir)
    ->get();




$search = $request->input('search.value');
$query = $this->creditVoucher
    ->select('id', 'voucher_no', 'date', 'approved_by', 'project_id', 'updated_by', 'note')
    ->with([
        'user:id,name',
        'project:id,name',
        'updatedBy:id,name'
    ])
    ->withSum('details as total_amount', 'debit');

if (!empty($search)) {
    $query->where(function ($q) use ($search) {

        $q->where('voucher_no', 'like', "%{$search}%")
            ->orWhere('date', 'like', "{$search}%")
            ->orWhere('note', 'like', "%{$search}%")

            ->orWhereHas('user', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            })

            ->orWhereHas('project', function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%");
            });
    });
}

$totalData = $this->creditVoucher->count();
$totalFiltered  = (clone $query)->count();

$creditVoucher = $query
    ->offset($start)
    ->limit($limit)
    ->orderBy($order, $dir)
    ->get();


