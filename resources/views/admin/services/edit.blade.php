@extends('admin.layout')

@section('pageTitle', 'Edit Service')

@section('content')
    <div class="form-card glass-card">
        <div class="form-header">
            <div>
                <p class="eyebrow">Edit Service</p>
                <h2>{{ $service->name }}</h2>
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

        <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data" class="artwork-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Informasi Service</h3>

                <div class="form-group">
                    <label for="name">Nama Service *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" placeholder="Contoh: Headshot, Halfbody, Fullbody" autocomplete="off" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea name="description" id="description" rows="4" placeholder="Jelaskan tentang service Anda..." autocomplete="off">{{ old('description', $service->description) }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="starting_price">Harga Awal (USD) *</label>
                    <input type="number" name="starting_price" id="starting_price" value="{{ old('starting_price', $service->starting_price) }}" min="0" autocomplete="off" required>
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
Flat color / Shaded">{{ old('features', is_array($service->features_array) ? implode("\n", $service->features_array) : '') }}</textarea>
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
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                        <span>Aktifkan service ini</span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="sort_order">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $service->sort_order) }}" min="0">
                    <small class="muted-text">Angka kecil akan tampil lebih dulu</small>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.services.index') }}" class="button button-outline">Batal</a>
                <button type="submit" class="button button-primary">Update Service</button>
            </div>
        </form>
    </div>
@endsection