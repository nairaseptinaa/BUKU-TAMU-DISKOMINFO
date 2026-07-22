@extends('layouts.admin')

@section('title', 'Edit Data Tamu')

@section('content')

<div class="card-panel form-narrow">
    <h2>Edit Data Tamu</h2>

    @if ($errors->any())
        <div class="error-box">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.guests.update', $guest) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" class="input-text"
                   value="{{ old('name', $guest->name) }}" required>
        </div>

        <div class="form-group">
            <label for="position">Jabatan</label>
            <input type="text" name="position" id="position" class="input-text"
                   value="{{ old('position', $guest->position) }}" required>
        </div>

        <div class="form-group">
            <label for="phone_number">No. WhatsApp / HP</label>
            <input type="text" name="phone_number" id="phone_number" class="input-text"
                   value="{{ old('phone_number', $guest->phone_number) }}">
        </div>

        <div class="form-group">
            <label for="visitor_type">Tipe Kunjungan</label>
            <select name="visitor_type" id="visitor_type" class="input-text" required>
                <option value="internal" {{ old('visitor_type', $guest->visitor_type) == 'internal' ? 'selected' : '' }}>Internal Pemkab</option>
                <option value="external" {{ old('visitor_type', $guest->visitor_type) == 'external' ? 'selected' : '' }}>Eksternal / Umum</option>
            </select>
        </div>

        <div class="form-group">
            <label for="department_id">Perangkat Daerah (jika Internal)</label>
            <select name="department_id" id="department_id" class="input-text">
                <option value="">-- Tidak Berlaku --</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ old('department_id', $guest->department_id) == $department->id ? 'selected' : '' }}>
                        {{ $department->department_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="external_agency">Nama Instansi (jika Eksternal)</label>
            <input type="text" name="external_agency" id="external_agency" class="input-text"
                   value="{{ old('external_agency', $guest->external_agency) }}">
        </div>

        <div class="form-group">
            <label for="service_type_id">Tujuan Kunjungan</label>
            <select name="service_type_id" id="service_type_id" class="input-text" required>
                @foreach ($serviceTypes as $serviceType)
                    <option value="{{ $serviceType->id }}" {{ old('service_type_id', $guest->service_type_id) == $serviceType->id ? 'selected' : '' }}>
                        {{ $serviceType->service_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="feedback">Kritik dan Saran</label>
            <textarea name="feedback" id="feedback" class="input-text">{{ old('feedback', $guest->feedback) }}</textarea>
        </div>

        <button type="submit" class="btn-save">Simpan Perubahan</button>
        <a href="{{ route('admin.dashboard') }}" class="back-link">&larr; Kembali ke Dashboard</a>
    </form>
</div>

@endsection