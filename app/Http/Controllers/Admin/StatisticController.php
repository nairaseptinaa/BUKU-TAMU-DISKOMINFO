<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guestbook;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $chartPeriod = $request->input('chart_period', 'last_7_days');

        switch ($chartPeriod) {
            case 'this_month':
                $chartStart = $now->copy()->startOfMonth();
                $chartEnd = $now->copy()->endOfMonth();
                break;
            case 'last_month':
                $chartStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $chartEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();
                break;
            default: // last_7_days
                $chartStart = $now->copy()->subDays(6)->startOfDay();
                $chartEnd = $now->copy()->endOfDay();
                $chartPeriod = 'last_7_days';
                break;
        }

        // ===== DATA GRAFIK GARIS (per hari, dalam rentang terpilih) =====
        $dailyLabels = [];
        $dailyValues = [];
        $cursor = $chartStart->copy();

        while ($cursor->lte($chartEnd)) {
            $count = Guestbook::whereDate('visit_date', $cursor)->count();
            $dailyLabels[] = $cursor->translatedFormat('d M');
            $dailyValues[] = $count;
            $cursor->addDay();
        }

        // ===== TABEL RIWAYAT HARIAN (cuma tanggal yang ada tamunya, terbaru dulu) =====
        $dailyReport = Guestbook::selectRaw('DATE(visit_date) as tanggal, count(*) as total')
            ->whereBetween('visit_date', [$chartStart, $chartEnd])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('admin.statistics', compact(
            'dailyLabels', 'dailyValues', 'chartPeriod', 'dailyReport'
        ));
    }

    public function printReport(Request $request)
    {
        $now = Carbon::now();
        $chartPeriod = $request->input('chart_period', 'last_7_days');

        switch ($chartPeriod) {
            case 'this_month':
                $chartStart = $now->copy()->startOfMonth();
                $chartEnd = $now->copy()->endOfMonth();
                $labelPeriod = 'Bulan Ini (' . $chartStart->translatedFormat('F Y') . ')';
                break;
            case 'last_month':
                $chartStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $chartEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();
                $labelPeriod = 'Bulan Lalu (' . $chartStart->translatedFormat('F Y') . ')';
                break;
            default: // last_7_days
                $chartStart = $now->copy()->subDays(6)->startOfDay();
                $chartEnd = $now->copy()->endOfDay();
                $chartPeriod = 'last_7_days';
                $labelPeriod = '7 Hari Terakhir (' . $chartStart->translatedFormat('d M Y') . ' - ' . $chartEnd->translatedFormat('d M Y') . ')';
                break;
        }

        $guests = Guestbook::with(['department', 'serviceType'])
            ->whereBetween('visit_date', [$chartStart, $chartEnd])
            ->orderBy('visit_date', 'asc')
            ->get();

        $totalToday    = Guestbook::whereDate('visit_date', Carbon::today())->count();
        $totalInternal = $guests->where('visitor_type', 'internal')->count();
        $totalExternal = $guests->where('visitor_type', 'external')->count();
        $totalPeriod   = $guests->count();

        return view('admin.report', compact(
            'guests', 'totalToday', 'totalInternal', 'totalExternal', 'totalPeriod', 'labelPeriod'
        ));
    }
}