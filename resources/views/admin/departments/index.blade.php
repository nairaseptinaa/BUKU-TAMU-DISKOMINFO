@extends('layouts.admin')

@section('title', 'Kelola PD/Unit Kerja')

@section('content')

<div class="card-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h2>Daftar PD/Unit Kerja</h2>
        <a href="{{ route('admin.departments.create') }}" class="btn-search">
            <i class="ph ph-plus"></i> Tambah Baru
        </a>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama PD/Unit Kerja</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departments as $department)
                    <tr>
                        <td>{{ $loop->iteration + ($departments->currentPage() - 1) * $departments->perPage() }}</td>
                        <td><strong>{{ $department->department_name }}</strong></td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('admin.departments.edit', $department) }}" class="btn-search btn-edit">
                                    <i class="ph ph-pencil"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-search btn-delete">
                                        <i class="ph ph-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="empty-row">Belum ada data PD/Unit Kerja.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $departments->links() }}
    </div>
</div>

@endsection