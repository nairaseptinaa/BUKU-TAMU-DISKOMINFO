<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::orderBy('service_name')->paginate(10);
        return view('admin.service-types.index', compact('serviceTypes'));
    }

    public function create()
    {
        return view('admin.service-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:150|unique:service_types,service_name',
        ]);

        ServiceType::create($validated);

        return redirect()->route('admin.service-types.index')->with('success', 'Jenis Layanan berhasil ditambahkan.');
    }

    public function edit(ServiceType $serviceType)
    {
        return view('admin.service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:150|unique:service_types,service_name,' . $serviceType->id,
        ]);

        $serviceType->update($validated);

        return redirect()->route('admin.service-types.index')->with('success', 'Jenis Layanan berhasil diperbarui.');
    }

    public function destroy(ServiceType $serviceType)
    {
        $serviceType->delete();

        return redirect()->route('admin.service-types.index')->with('success', 'Jenis Layanan berhasil dihapus.');
    }
}