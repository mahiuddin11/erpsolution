<?php

namespace App\Http\Controllers\Backend\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chart;
use App\Models\Expense;
use App\Models\Purchases;
use App\Models\Sale;
use App\Models\StockSummary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;


class ChartController extends Controller
{

    public function index()
    {
        $year = ['2015', '2016', '2017', '2018', '2019', '2020'];

        $user = [];
        foreach ($year as $key => $value) {
            $user[] = User::where(DB::raw("DATE_FORMAT(created_at, '%Y')"), $value)->count();
        }
        return response()->with('year', json_encode($year, JSON_NUMERIC_CHECK))->with()->with('user', json_encode($user, JSON_NUMERIC_CHECK));
        // return view('chartjs')->with('year', json_encode($year, JSON_NUMERIC_CHECK))->with('user', json_encode($user, JSON_NUMERIC_CHECK));
    }
    public function chart()
    {
        // $this->googlePieChart();
        $result = StockSummary::join('branches', 'branches.id', '=', 'stock_summaries.branch_id')
            ->orderBy('stock_summaries.branch_id', 'asc')
            ->where("stock_summaries.type", 'Branch')
            ->groupBy('branch_id')
            ->get(['branches.branchCode', 'stock_summaries.quantity']);
        //   /      pops($result);
        return response()->json($result);
    }
}
