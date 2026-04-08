<?php

namespace App\Http\Controllers\Backend\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Services\Hrm\HolidayService;
use Carbon\Carbon;
use Illuminate\Http\Request;


class HolidayController extends Controller
{
     
    protected $holidayService;

    public function __construct(HolidayService $holidayService)
    {
        $this->holidayService = $holidayService;
    }

    /* ══════════════════════════════════════════════
     | Holiday Setup index page
     ══════════════════════════════════════════════ */
    public function index()
    {
        $title       = 'Holiday Setup';
        $currentYear = (int) now()->year;

        $runningMonths = $this->buildMonthsForYear($currentYear);

        $previousYears = [];
        for ($y = $currentYear - 1; $y >= $currentYear - 3; $y--) {
            $previousYears[$y] = $this->buildMonthsForYear($y);
        }

       
        return view('backend.pages.hrm.holiday.index', get_defined_vars());
    }

   
   
    public function getMonthHolidays(Request $request)
    {
        $year  = (int) $request->year;
        $month = (int) $request->month;

        $holidays = Holiday::forMonth($year, $month)
            ->select('id', 'date', 'title', 'note')  // include note column
            ->orderBy('date')
            ->get()
            ->map(fn($h) => [
                'id'    => $h->id,
                'date'  => $h->date->format('Y-m-d'),
                'title' => $h->title ?? 'Holiday',
                'note'  => $h->note  ?? '',
            ]);

        return response()->json(['success' => true, 'holidays' => $holidays]);
    }

    /* ══════════════════════════════════════════════
     | API: Save holidays for a month
     | POST /hrm/holiday/save
     |
     | Body (JSON):
     | {
     |   year:  2026,
     |   month: 4,
     |   dates: [
     |     { date: '2026-04-12', note: 'Independence Day' },
     |     { date: '2026-04-14', note: '' },
     |   ]
     | }
     ══════════════════════════════════════════════ */
    public function save(Request $request)
    {
        $request->validate([
            'year'         => 'required|integer|min:2000|max:2100',
            'month'        => 'required|integer|min:1|max:12',
            'dates'        => 'present|array',
            'dates.*.date' => 'required|date_format:Y-m-d',
            'dates.*.note' => 'nullable|string|max:200',
        ]);

        $year  = (int) $request->year;
        $month = (int) $request->month;

        // Deduplicate by date
        $datesInput = collect($request->dates)
            ->unique('date')
            ->values();

        // Delete all existing holidays for this month, then re-insert
        Holiday::forMonth($year, $month)->delete();

        $now  = now();
        $rows = $datesInput->map(fn($item) => [
            'date'       => $item['date'],
            'year'       => $year,
            'month'      => $month,
            'title'      => 'Holiday',
            'note'       => $item['note'] ?? '',
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        if (!empty($rows)) {
            Holiday::insert($rows);
        }

        $count = count($rows);

        return response()->json([
            'success' => true,
            'message' => $count . ' holiday' . ($count !== 1 ? 's' : '') . ' saved for '
                . Carbon::create($year, $month)->format('F Y'),
            'count'   => $count,
        ]);
    }

    /* ══════════════════════════════════════════════
     | Helper: build month list with holiday counts
     |
     | FIX 1: Only counts rows in holidays table.
     |         Friday is NOT counted here.
     |         Friday handling is done in AttendanceLogController
     |         and client-side calendar JS.
     ══════════════════════════════════════════════ */
    private function buildMonthsForYear(int $year): array
    {
        // One query: count manually-set holidays per month (Friday NOT included)
        $counts = Holiday::forYear($year)
            ->selectRaw('month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();



        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = [
                'number'        => $m,
                'name'          => Carbon::create($year, $m)->format('F'),
                'holiday_count' => $counts[$m] ?? 0,  // 0 if no manual holidays, Friday never counted
            ];
        }

        return $months;
    }


}
