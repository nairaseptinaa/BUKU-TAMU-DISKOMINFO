<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('department_name')->paginate(10);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:150|unique:departments,department_name',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')->with('success', 'PD/Unit Kerja berhasil ditambahkan.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'department_name' => 'required|string|max:150|unique:departments,department_name,' . $department->id,
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')->with('success', 'PD/Unit Kerja berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'PD/Unit Kerja berhasil dihapus.');
    }
}