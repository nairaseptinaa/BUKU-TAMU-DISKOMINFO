<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ServiceTypeController extends Controller
{
    public function index(): View
    {
        $serviceTypes = ServiceType::query()
            ->withCount('guestbooks')
            ->orderBy('service_name')
            ->paginate(10);

        return view('admin.service-types.index', compact('serviceTypes'));
    }

    public function create(): View
    {
        return view('admin.service-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255', 'unique:service_types,service_name'],
        ]);

        ServiceType::create($validated);

        return redirect()
            ->route('admin.service-types.index')
            ->with('success', 'Jenis layanan berhasil ditambahkan.');
    }

    public function edit(ServiceType $serviceType): View
    {
        return view('admin.service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, ServiceType $serviceType): RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('service_types', 'service_name')->ignore($serviceType->id),
            ],
        ]);

        $serviceType->update($validated);

        return redirect()
            ->route('admin.service-types.index')
            ->with('success', 'Jenis layanan berhasil diperbarui.');
    }

    public function destroy(ServiceType $serviceType): RedirectResponse
    {
        if ($serviceType->guestbooks()->exists()) {
            return back()->with(
                'error',
                'Jenis layanan tidak dapat dihapus karena masih dipakai pada data kunjungan.'
            );
        }

        $serviceType->delete();

        return redirect()
            ->route('admin.service-types.index')
            ->with('success', 'Jenis layanan berhasil dihapus.');
    }
}
