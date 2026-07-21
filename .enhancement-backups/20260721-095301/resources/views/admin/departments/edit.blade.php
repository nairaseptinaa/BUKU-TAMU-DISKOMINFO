@extends('layouts.admin')

@section('title', 'Edit PD/Unit Kerja')

@section('content')
    <div class="card-panel form-narrow">
        <h2>Edit PD/Unit Kerja</h2>
        <p class="form-description">Periksa kembali nama sebelum menyimpan. Riwayat kunjungan terkait akan memakai nama terbaru.</p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first('department_name') }}</div>
        @endif

        <form
            method="POST"
            action="{{ route('admin.departments.update', $department) }}"
            data-confirm-title="Simpan perubahan?"
            data-confirm="Nama PD/Unit Kerja akan diubah dari “{{ $department->department_name }}” menjadi nilai yang baru Anda masukkan."
            data-confirm-button="Ya, Simpan"
            data-confirm-tone="warning"
        >
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="department_name">Nama PD/Unit Kerja</label>
                <input
                    type="text"
                    name="department_name"
                    id="department_name"
                    class="input-text"
                    value="{{ old('department_name', $department->department_name) }}"
                    maxlength="255"
                    required
                    autofocus
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-save">
                    <i class="ph ph-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.departments.index') }}" class="back-link">&larr; Kembali ke Daftar</a>
            </div>
        </form>
    </div>
@endsection
