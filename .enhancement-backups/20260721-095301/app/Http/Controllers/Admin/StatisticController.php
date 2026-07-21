<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guestbook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class StatisticController extends Controller
{
    public function index(Request $request): View
    {
        $period = $this->normalizePeriod((string) $request->input('period', $request->input('chart_period', 'last_7_days')));
        $statistics = $this->buildStatistics($period);

        return view('admin.statistics', array_merge($statistics, [
            'period' => $period,
        ]));
    }

    public function printReport(Request $request): View
    {
        $period = $this->normalizePeriod((string) $request->input('period', $request->input('chart_period', 'last_7_days')));
        $statistics = $this->buildStatistics($period);

        $guests = Guestbook::query()
            ->with(['department', 'serviceType'])
            ->whereBetween('visit_date', [$statistics['startDate'], $statistics['endDate']])
            ->orderByDesc('visit_date')
            ->get();

        return view('admin.report', array_merge($statistics, [
            'period' => $period,
            'guests' => $guests,
            'generatedAt' => Carbon::now(),
        ]));
    }

    private function normalizePeriod(string $period): string
    {
        return in_array($period, ['last_7_days', 'this_month', 'last_month'], true)
            ? $period
            : 'last_7_days';
    }

    /**
     * Menyusun data statistik dalam query agregasi, bukan satu query per hari.
     *
     * @return array<string, mixed>
     */
    private function buildStatistics(string $period): array
    {
        Carbon::setLocale('id');
        [$startDate, $endDate, $periodLabel] = $this->resolvePeriod($period);

        $dailyCounts = Guestbook::query()
            ->selectRaw('DATE(visit_date) as visit_day, COUNT(*) as total')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->groupBy('visit_day')
            ->orderBy('visit_day')
            ->pluck('total', 'visit_day');

        $dailyRows = $this->fillDailyRows($startDate, $endDate, $dailyCounts);
        $totalGuests = (int) $dailyRows->sum('total');
        $daysInPeriod = max(1, $dailyRows->count());
        $averagePerDay = round($totalGuests / $daysInPeriod, 1);

        $busiestDay = $dailyRows->sortByDesc('total')->first();
        $busiestDayLabel = $busiestDay && $busiestDay->total > 0
            ? Carbon::parse($busiestDay->date)->translatedFormat('d F Y')
            : '-';
        $busiestDayTotal = $busiestDay?->total ?? 0;

        $visitorTypeCounts = Guestbook::query()
            ->selectRaw('visitor_type, COUNT(*) as total')
            ->whereBetween('visit_date', [$startDate, $endDate])
            ->groupBy('visitor_type')
            ->pluck('total', 'visitor_type');

        $internalGuests = (int) ($visitorTypeCounts['internal'] ?? 0);
        $externalGuests = (int) ($visitorTypeCounts['external'] ?? 0);
        $internalPercentage = $totalGuests > 0
            ? round(($internalGuests / $totalGuests) * 100, 1)
            : 0;
        $externalPercentage = $totalGuests > 0
            ? round(($externalGuests / $totalGuests) * 100, 1)
            : 0;

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'periodLabel' => $periodLabel,
            'dailyLabels' => $dailyRows->pluck('shortLabel')->values(),
            'dailyValues' => $dailyRows->pluck('total')->values(),
            'dailyReport' => $dailyRows->sortByDesc('date')->values(),
            'dailyReportChronological' => $dailyRows->values(),
            'totalGuests' => $totalGuests,
            'averagePerDay' => $averagePerDay,
            'busiestDayLabel' => $busiestDayLabel,
            'busiestDayTotal' => $busiestDayTotal,
            'internalGuests' => $internalGuests,
            'externalGuests' => $externalGuests,
            'internalPercentage' => $internalPercentage,
            'externalPercentage' => $externalPercentage,
        ];
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    private function resolvePeriod(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'this_month' => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfDay(),
                'Bulan Ini',
            ],
            'last_month' => [
                $now->copy()->subMonthNoOverflow()->startOfMonth(),
                $now->copy()->subMonthNoOverflow()->endOfMonth(),
                'Bulan Lalu',
            ],
            default => [
                $now->copy()->subDays(6)->startOfDay(),
                $now->copy()->endOfDay(),
                '7 Hari Terakhir',
            ],
        };
    }

    /**
     * @param Collection<string, int|string> $dailyCounts
     * @return Collection<int, object{date: string, shortLabel: string, fullLabel: string, total: int}>
     */
    private function fillDailyRows(Carbon $startDate, Carbon $endDate, Collection $dailyCounts): Collection
    {
        $rows = collect();
        $cursor = $startDate->copy()->startOfDay();
        $lastDay = $endDate->copy()->startOfDay();

        while ($cursor->lte($lastDay)) {
            $dateKey = $cursor->toDateString();

            $rows->push((object) [
                'date' => $dateKey,
                'shortLabel' => $cursor->translatedFormat('d M'),
                'fullLabel' => $cursor->translatedFormat('d F Y'),
                'total' => (int) ($dailyCounts[$dateKey] ?? 0),
            ]);

            $cursor->addDay();
        }

        return $rows;
    }
}
