<?php

namespace App\Http\Controllers;

use App\Models\Guestbook;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $today        = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalToday    = Guestbook::whereDate('visit_date', $today)->count();
        $totalMonth    = Guestbook::where('visit_date', '>=', $startOfMonth)->count();
        $totalInternal = Guestbook::where('visitor_type', 'internal')
                            ->where('visit_date', '>=', $startOfMonth)->count();
        $totalExternal = Guestbook::where('visitor_type', 'external')
                            ->where('visit_date', '>=', $startOfMonth)->count();

        $query = Guestbook::with(['department', 'serviceType'])->orderBy('visit_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('visitor_type')) {
            $query->where('visitor_type', $request->input('visitor_type'));
        }

        $guests = $query->paginate(10);
        $skmUrl = SystemSetting::get('skm_redirect_url', 'https://skm.go.id');

        return view('admin.dashboard', compact(
            'totalToday', 'totalMonth', 'totalInternal', 'totalExternal',
            'guests', 'skmUrl'
        ));
    }

    public function updateSkm(Request $request)
    {
        $request->validate(['skm_redirect_url' => 'required|url']);

        SystemSetting::updateOrCreate(
            ['setting_key' => 'skm_redirect_url'],
            ['setting_value' => $request->input('skm_redirect_url')]
        );

        return redirect()->back()->with('success', 'Tautan SKM berhasil diperbarui!');
    }

    public function settings()
    {
        $skmUrl = SystemSetting::get('skm_redirect_url', 'https://skm.go.id');
        return view('admin.settings', compact('skmUrl'));
    }
}