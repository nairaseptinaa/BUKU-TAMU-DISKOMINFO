<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::query()
            ->withCount('guestbooks')
            ->orderBy('department_name')
            ->paginate(10);

        return view('admin.departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'department_name' => ['required', 'string', 'max:255', 'unique:departments,department_name'],
        ]);

        Department::create($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'PD/Unit kerja berhasil ditambahkan.');
    }

    public function edit(Department $department): View
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'department_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'department_name')->ignore($department->id),
            ],
        ]);

        $department->update($validated);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'PD/Unit kerja berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->guestbooks()->exists()) {
            return back()->with(
                'error',
                'PD/Unit kerja tidak dapat dihapus karena masih dipakai pada data kunjungan.'
            );
        }

        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'PD/Unit kerja berhasil dihapus.');
    }
}
