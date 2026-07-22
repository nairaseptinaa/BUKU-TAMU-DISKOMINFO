<?php

namespace App\Http\Controllers;

use App\Models\Guestbook;
use App\Models\SystemSetting;
use App\Models\ServiceType;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Tampilkan dashboard admin beserta pencarian dan filter tamu.
     */
    public function index(Request $request): View|JsonResponse
    {
        Carbon::setLocale('id');
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $todayGuests = Guestbook::whereDate('visit_date', $today)->count();
        $monthlyGuests = Guestbook::whereBetween('visit_date', [
            $startOfMonth,
            Carbon::now()->endOfDay(),
        ])->count();
        $internalGuests = Guestbook::where('visitor_type', 'internal')
            ->whereBetween('visit_date', [$startOfMonth, Carbon::now()->endOfDay()])
            ->count();
        $externalGuests = Guestbook::where('visitor_type', 'external')
            ->whereBetween('visit_date', [$startOfMonth, Carbon::now()->endOfDay()])
            ->count();

        $search = trim((string) $request->input('search', ''));
        $visitorType = (string) $request->input('visitor_type', '');
        $period = (string) $request->input('period', 'last_7_days');

        if (! in_array($visitorType, ['', 'internal', 'external'], true)) {
            $visitorType = '';
        }

        if (! in_array($period, ['last_7_days', 'last_1_month', 'all'], true)) {
            $period = 'last_7_days';
        }

        $query = Guestbook::query()
            ->with(['department', 'serviceType']);

        if ($search !== '') {
            $query->where(function ($guestQuery) use ($search): void {
                $keyword = "%{$search}%";

                $guestQuery
                    ->where('name', 'like', $keyword)
                    ->orWhere('phone_number', 'like', $keyword)
                    ->orWhere('position', 'like', $keyword)
                    ->orWhere('external_agency', 'like', $keyword)
                    ->orWhereHas('department', function ($departmentQuery) use ($keyword): void {
                        $departmentQuery->where('department_name', 'like', $keyword);
                    })
                    ->orWhereHas('serviceType', function ($serviceTypeQuery) use ($keyword): void {
                        $serviceTypeQuery->where('service_name', 'like', $keyword);
                    });
            });
        }

        if ($visitorType !== '') {
            $query->where('visitor_type', $visitorType);
        }

        match ($period) {
            'last_7_days' => $query->whereBetween('visit_date', [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay(),
            ]),
            'last_1_month' => $query->whereBetween('visit_date', [
                Carbon::now()->subMonth()->startOfDay(),
                Carbon::now()->endOfDay(),
            ]),
            default => null,
        };

        $guests = $query
            ->orderByDesc('visit_date')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.partials.guest-table', compact('guests'))->render(),
                'total' => $guests->total(),
            ]);
        }

        return view('admin.dashboard', compact(
            'todayGuests',
            'monthlyGuests',
            'internalGuests',
            'externalGuests',
            'guests',
            'search',
            'visitorType',
            'period',
        ));
    }

    public function updateSkm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'skm_redirect_url' => ['required', 'url'],
        ]);

        SystemSetting::updateOrCreate(
            ['setting_key' => 'skm_redirect_url'],
            ['setting_value' => $validated['skm_redirect_url']]
        );

        return back()->with('success', 'Tautan SKM berhasil diperbarui.');
    }

    public function settings(): View
    {
        $skmUrl = SystemSetting::get('skm_redirect_url', 'https://skm.go.id');

        return view('admin.settings', compact('skmUrl'));
    }

    public function editGuest(Guestbook $guest)
    {
        $departments = Department::all();
        $serviceTypes = ServiceType::all();

        return view('admin.guests.edit', compact('guest', 'departments', 'serviceTypes'));
    }

    public function updateGuest(Request $request, Guestbook $guest)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'position' => 'required|string|max:100',
            'visitor_type' => 'required|in:internal,external',
            'department_id' => 'nullable|required_if:visitor_type,internal|exists:departments,id',
            'external_agency' => 'nullable|required_if:visitor_type,external|string|max:150',
            'phone_number' => 'nullable|string|max:20',
            'service_type_id' => 'required|exists:service_types,id',
            'feedback' => 'nullable|string',
        ]);

        $guest->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'Data tamu berhasil diperbarui.');
    }

    public function destroyGuest(Guestbook $guest)
    {
        $guest->delete();

        return redirect()->route('admin.dashboard')->with('success', 'Data tamu berhasil dihapus.');
    }
}
