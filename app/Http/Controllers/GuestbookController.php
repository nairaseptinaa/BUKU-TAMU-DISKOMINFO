<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ServiceType;
use App\Models\Guestbook;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class GuestbookController extends Controller
{
    public function create()
    {
        $departments = Department::all();
        $serviceTypes = ServiceType::all();

        return view('guestbook.form', [
            'departments' => $departments,
            'serviceTypes' => $serviceTypes,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'position' => 'required|string|max:100',
            'visitor_type' => 'required|in:internal,external',
            'department_id' => 'nullable|required_if:visitor_type,internal|exists:departments,id',
            'external_agency' => 'nullable|required_if:visitor_type,external|string|max:150',
            'phone_number' => ['nullable', 'regex:/^[0-9+\-\s]+$/', 'max:20'],
            'service_type_id' => 'required|exists:service_types,id',
            'feedback' => 'nullable|string',
        ]);

        Guestbook::create($validated);

        $skmUrl = SystemSetting::get('skm_redirect_url', 'https://skm.go.id');

        return redirect()->away($skmUrl);
    }
}