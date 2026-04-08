<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'year', 'month', 'title','note'];

    protected $casts = [
        'date' => 'date',
    ];

    /* ── Scopes ── */

    public function scopeForYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    /**
     * Returns a flat array of date strings for O(1) lookup.
     * Used by AttendanceLogController.
     * Friday is NOT included here — handled separately in the controller.
     *
     * @return array  e.g. ['2026-04-12' => 0, '2026-04-14' => 0]
     */
    public static function getHolidaySet(?string $start = null, ?string $end = null): array
    {
        $q = static::query();

        if ($start && $end) {
            $q->whereBetween('date', [$start, $end]);
        }

        return $q->pluck('date')
            ->map(fn($d) => (string) $d)
            ->flip()
            ->toArray();
    }
}
