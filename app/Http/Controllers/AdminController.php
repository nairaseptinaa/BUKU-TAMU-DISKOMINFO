<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Guestbook;
use App\Models\ServiceType;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin, statistik,
     * pencarian, filter, dan pagination data tamu.
     */
    public function index(Request $request): View|JsonResponse
    {
        Carbon::setLocale('id');

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfToday = $now->copy()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Statistik dashboard
        |--------------------------------------------------------------------------
        */

        $todayGuests = Guestbook::whereDate('visit_date', $today)->count();

        $monthlyGuests = Guestbook::whereBetween('visit_date', [
            $startOfMonth,
            $endOfToday,
        ])->count();

        $internalGuests = Guestbook::where('visitor_type', 'internal')
            ->whereBetween('visit_date', [
                $startOfMonth,
                $endOfToday,
            ])
            ->count();

        $externalGuests = Guestbook::where('visitor_type', 'external')
            ->whereBetween('visit_date', [
                $startOfMonth,
                $endOfToday,
            ])
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Ambil dan validasi parameter filter
        |--------------------------------------------------------------------------
        */

        $search = trim((string) $request->input('search', ''));
        $visitorType = (string) $request->input('visitor_type', '');
        $period = (string) $request->input('period', 'last_7_days');

        $allowedVisitorTypes = [
            '',
            'internal',
            'external',
        ];

        $allowedPeriods = [
            'last_7_days',
            'last_1_month',
            'all',
        ];

        if (! in_array($visitorType, $allowedVisitorTypes, true)) {
            $visitorType = '';
        }

        if (! in_array($period, $allowedPeriods, true)) {
            $period = 'last_7_days';
        }

        /*
        |--------------------------------------------------------------------------
        | Query utama data tamu
        |--------------------------------------------------------------------------
        */

        $query = Guestbook::query()
            ->with([
                'department',
                'serviceType',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Filter pencarian
        |--------------------------------------------------------------------------
        */

        if ($search !== '') {
            $query->where(function ($guestQuery) use ($search): void {
                $keyword = "%{$search}%";

                $guestQuery
                    ->where('name', 'like', $keyword)
                    ->orWhere('phone_number', 'like', $keyword)
                    ->orWhere('position', 'like', $keyword)
                    ->orWhere('external_agency', 'like', $keyword)
                    ->orWhereHas(
                        'department',
                        function ($departmentQuery) use ($keyword): void {
                            $departmentQuery->where(
                                'department_name',
                                'like',
                                $keyword
                            );
                        }
                    )
                    ->orWhereHas(
                        'serviceType',
                        function ($serviceTypeQuery) use ($keyword): void {
                            $serviceTypeQuery->where(
                                'service_name',
                                'like',
                                $keyword
                            );
                        }
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter jenis tamu
        |--------------------------------------------------------------------------
        */

        if ($visitorType !== '') {
            $query->where('visitor_type', $visitorType);
        }

        /*
        |--------------------------------------------------------------------------
        | Filter periode kunjungan
        |--------------------------------------------------------------------------
        */

        if ($period === 'last_7_days') {
            $query->whereBetween('visit_date', [
                $now->copy()->subDays(6)->startOfDay(),
                $endOfToday,
            ]);
        }

        if ($period === 'last_1_month') {
            $query->whereBetween('visit_date', [
                $now->copy()->subMonth()->startOfDay(),
                $endOfToday,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        |
        | Menampilkan 8 data per halaman agar dashboard lebih ringkas.
        | withQueryString mempertahankan filter saat pindah halaman.
        |
        */

        $guests = $query
            ->orderByDesc('visit_date')
            ->orderByDesc('id')
            ->paginate(5)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Respons AJAX
        |--------------------------------------------------------------------------
        */

        if ($request->ajax()) {
            return response()->json([
                'html' => view(
                    'admin.partials.guest-table',
                    compact('guests')
                )->render(),
                'total' => $guests->total(),
                'current_page' => $guests->currentPage(),
                'last_page' => $guests->lastPage(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Respons halaman biasa
        |--------------------------------------------------------------------------
        */

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

    /**
     * Memperbarui tautan SKM.
     */
    public function updateSkm(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'skm_redirect_url' => [
                'required',
                'url',
            ],
        ]);

        SystemSetting::updateOrCreate(
            [
                'setting_key' => 'skm_redirect_url',
            ],
            [
                'setting_value' => $validated['skm_redirect_url'],
            ]
        );

        return back()->with(
            'success',
            'Tautan SKM berhasil diperbarui.'
        );
    }

    /**
     * Menampilkan halaman pengaturan.
     */
    public function settings(): View
    {
        $skmUrl = SystemSetting::get(
            'skm_redirect_url',
            'https://skm.go.id'
        );

        return view('admin.settings', compact('skmUrl'));
    }

    /**
     * Menampilkan form edit data tamu.
     */
    public function editGuest(Guestbook $guest): View
    {
        $departments = Department::orderBy('department_name')->get();
        $serviceTypes = ServiceType::orderBy('service_name')->get();

        return view(
            'admin.guests.edit',
            compact(
                'guest',
                'departments',
                'serviceTypes'
            )
        );
    }

    /**
     * Memperbarui data tamu.
     */
    public function updateGuest(
        Request $request,
        Guestbook $guest
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'position' => [
                'required',
                'string',
                'max:100',
            ],
            'visitor_type' => [
                'required',
                'in:internal,external',
            ],
            'department_id' => [
                'nullable',
                'required_if:visitor_type,internal',
                'exists:departments,id',
            ],
            'external_agency' => [
                'nullable',
                'required_if:visitor_type,external',
                'string',
                'max:150',
            ],
            'phone_number' => [
                'nullable',
                'regex:/^[0-9+\-\s]+$/',
                'max:20',
            ],
            'service_type_id' => [
                'required',
                'exists:service_types,id',
            ],
            'feedback' => [
                'nullable',
                'string',
            ],
        ]);

        /*
         * Bersihkan field yang tidak relevan berdasarkan jenis tamu.
         */
        if ($validated['visitor_type'] === 'internal') {
            $validated['external_agency'] = null;
        }

        if ($validated['visitor_type'] === 'external') {
            $validated['department_id'] = null;
        }

        $guest->update($validated);

        return redirect()
            ->route('admin.dashboard')
            ->with(
                'success',
                'Data tamu berhasil diperbarui.'
            );
    }

    /**
     * Menghapus data tamu.
     */
    public function destroyGuest(
        Guestbook $guest
    ): RedirectResponse {
        $guest->delete();

        return redirect()
            ->route('admin.dashboard')
            ->with(
                'success',
                'Data tamu berhasil dihapus.'
            );
    }
}
