@extends('layouts.admin')

@section('title', 'Edit Jenis Layanan')

@section('content')

<div class="card-panel form-narrow">
    <h2>Edit Jenis Layanan</h2>

    @if ($errors->any())
        <div class="error-box">
            {{ $errors->first('service_name') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.service-types.update', $serviceType) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="service_name">Nama Jenis Layanan</label>
            <input type="text" name="service_name" id="service_name" class="input-text"
                   value="{{ old('service_name', $serviceType->service_name) }}" required autofocus>
        </div>
        <button type="submit" class="btn-save">
            <i class="ph ph-floppy-disk"></i> Simpan Perubahan
        </button>
        <a href="{{ route('admin.service-types.index') }}" class="back-link">
            &larr; Kembali ke Daftar
        </a>
    </form>
</div>

@endsection