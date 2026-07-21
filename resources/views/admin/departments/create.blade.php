@extends('layouts.admin')

@section('title', 'Tambah PD/Unit Kerja')

@section('content')

<div class="card-panel form-narrow">
    <h2>Tambah PD/Unit Kerja Baru</h2>

    @if ($errors->any())
        <div class="error-box">
            {{ $errors->first('department_name') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.departments.store') }}">
        @csrf
        <div class="form-group">
            <label for="department_name">Nama PD/Unit Kerja</label>
            <input type="text" name="department_name" id="department_name" class="input-text"
                   placeholder="Cth: Bidang Aplikasi dan Informatika" value="{{ old('department_name') }}" required autofocus>
        </div>
        <button type="submit" class="btn-save">
            <i class="ph ph-floppy-disk"></i> Simpan
        </button>
        <a href="{{ route('admin.departments.index') }}" class="back-link">
            &larr; Kembali ke Daftar
        </a>
    </form>
</div>

@endsection