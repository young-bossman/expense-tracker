<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    

    /**
     * Show dashboard with summary & chart data.
     * Query params:
     *  - range: all / year / month / week
     *  - year: numeric (e.g. 2025)
     *  - month: numeric 1-12 (for month range)
     *  - start: ISO date for week start or use default
     */
    public function index(Request $request)
    {
        // Determine available years (descending)
        $years = Expense::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Default: most recent year with expenses, else current year
        $defaultYear = count($years) ? $years[0] : Carbon::now()->year;

        $range = $request->get('range', 'month'); // default show year
        $year = (int) $request->get('year', $defaultYear);
        $month = (int) $request->get('month', Carbon::now()->month);
        $start = $request->get('start'); // for week (optional)

        // Data containers
        $labels = [];
        $data = [];
        $cards = []; // for monthly summary cards when range == year

        if ($range === 'all') {
            // Group by year
            $rows = Expense::selectRaw('YEAR(date) as label, SUM(amount) as total')
                ->groupBy(DB::raw('YEAR(date)'))
                ->orderBy(DB::raw('YEAR(date)'))
                ->get();

            foreach ($rows as $r) {
                $labels[] = $r->label;
                $data[] = (float) $r->total;
            }

        } elseif ($range === 'month') {
            // Month range: show totals per day for a given year+month
            // Normalize month/year
            $startDate = Carbon::create($year, $month, 1)->startOfMonth();
            $endDate = (clone $startDate)->endOfMonth();

            $rows = Expense::selectRaw('DAY(date) as label, SUM(amount) as total')
                ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
                ->groupBy(DB::raw('DAY(date)'))
                ->orderBy(DB::raw('DAY(date)'))
                ->get();

            // Fill labels for all days (1..N) and 0 for missing days
            $daysInMonth = $startDate->daysInMonth;
            $map = $rows->pluck('total', 'label')->toArray();

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                $data[] = isset($map[$d]) ? (float)$map[$d] : 0.0;
            }

        } elseif ($range === 'week') {
            // Week range: show last 7 days or week containing 'start' param
            if ($start) {
                $weekStart = Carbon::parse($start)->startOfDay();
            } else {
                // default to start of current week (Monday)
                $weekStart = Carbon::now()->startOfWeek(); // Monday
            }
            $weekEnd = (clone $weekStart)->endOfWeek();

            $rows = Expense::selectRaw('DATE(date) as label, SUM(amount) as total')
                ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                ->groupBy(DB::raw('DATE(date)'))
                ->orderBy(DB::raw('DATE(date)'))
                ->get();

            $map = $rows->pluck('total', 'label')->toArray();

            // labels: Mon, Tue, ... Sun with dates appended
            $period = [];
            for ($i = 0; $i < 7; $i++) {
                $d = (clone $weekStart)->addDays($i);
                $label = $d->format('D M-d'); // e.g., Mon Apr-07
                $labels[] = $label;
                $data[] = isset($map[$d->toDateString()]) ? (float)$map[$d->toDateString()] : 0.0;
            }

        } else {
            // Default 'year' range: show months for selected year
            $rows = Expense::selectRaw('MONTH(date) as month, SUM(amount) as total')
                ->whereYear('date', $year)
                ->groupBy(DB::raw('MONTH(date)'))
                ->orderBy(DB::raw('MONTH(date)'))
                ->get();

            // Map month number -> total
            $map = $rows->pluck('total', 'month')->toArray();

            for ($m = 1; $m <= 12; $m++) {
                $labels[] = Carbon::create($year, $m, 1)->format('M'); // Jan, Feb...
                $data[] = isset($map[$m]) ? (float)$map[$m] : 0.0;

                // prepare cards: month label and total (for non-zero or zero)
                $cards[] = [
                    'label' => Carbon::create($year, $m, 1)->format('F'),
                    'total' => isset($map[$m]) ? (float)$map[$m] : 0.0,
                ];
            }
        }

        // Total for selected range (optional)
        $totalForRange = array_sum($data);

        // Pass to view
        return view('dashboard', [
            'years' => $years,
            'selectedYear' => $year,
            'range' => $range,
            'selectedMonth' => $month,
            'start' => $start,
            'labels' => $labels,
            'data' => $data,
            'cards' => $cards,
            'totalForRange' => $totalForRange,
        ]);
    }
}
