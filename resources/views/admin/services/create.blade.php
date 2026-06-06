@extends('admin.layout')

@section('pageTitle', 'Tambah Service Baru')

@section('content')
    <div class="form-card glass-card">
        <div class="form-header">
            <div>
                <p class="eyebrow">Tambah Service Baru</p>
                <h2>Tambahkan service commission baru</h2>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <h4>Validasi Gagal</h4>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" class="artwork-form" autocomplete="off">
            @csrf

            <div class="form-section">
                <h3>Informasi Service</h3>

                <div class="form-group">
                    <label for="name">Nama Service *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Contoh: Headshot, Halfbody, Fullbody" autocomplete="off" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" placeholder="Jelaskan tentang service Anda..." rows="4" autocomplete="off">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="starting_price">Harga Awal (USD) *</label>
                    <input type="number" name="starting_price" id="starting_price" value="{{ old('starting_price') }}" placeholder="50" min="0" required>
                    @error('starting_price')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Fitur Service</h3>

                <div class="form-group">
                    <label for="features">Fitur (satu baris per fitur)</label>
                    <textarea name="features" id="features" rows="5" placeholder="Simple background
Basic shading
1 character
Flat color / Shaded">{{ old('features') }}</textarea>
                    <small class="muted-text">Masukkan satu fitur per baris</small>
                    @error('features')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Pengaturan</h3>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span>Aktifkan service ini</span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="sort_order">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                    <small class="muted-text">Angka kecil akan tampil lebih dulu</small>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.services.index') }}" class="button button-outline">Batal</a>
                <button type="submit" class="button button-primary">Simpan Service</button>
            </div>
        </form>
    </div>
@endsection