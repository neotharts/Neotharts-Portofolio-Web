@extends('admin.layout')

@section('pageTitle', 'Tambah Artwork Baru')

@section('content')
    <div class="form-card glass-card">
        <div class="form-header">
            <div>
                <p class="eyebrow">Tambah Artwork Baru</p>
                <h2>Unggah karya seni baru ke dalam portfolio</h2>
            </div>
        </div>

        <!-- Alert Messages -->
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

        <form action="{{ route('admin.artworks.store') }}" method="POST" enctype="multipart/form-data" class="artwork-form">
            @csrf

            <div class="form-section">
                <h3>Informasi Dasar</h3>

                <div class="form-group">
                    <label for="title">Judul Artwork *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Masukkan judul artwork" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi *</label>
                    <textarea name="description" id="description" placeholder="Jelaskan tentang artwork Anda..." rows="6" required>{{ old('description') }}</textarea>
                    @error('description')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Kategori</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Tipe Artwork *</label>
                        <select name="type" id="type" required>
                            <option value="">Pilih tipe...</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="form">Form Artwork *</label>
                        <select name="form" id="form" required>
                            <option value="">Pilih form...</option>
                            @foreach($forms as $form)
                                <option value="{{ $form }}" {{ old('form') === $form ? 'selected' : '' }}>
                                    {{ ucfirst($form) }}
                                </option>
                            @endforeach
                        </select>
                        @error('form')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Gambar</h3>

                <div class="form-group">
                    <label for="image">Upload Gambar *</label>
                    <div class="file-upload">
                        <input type="file" name="image" id="image" accept="image/*" required onchange="previewImage(event)">
                        <div class="file-upload-area">
                            <span class="material-icons-outlined">cloud_upload</span>
                            <p>Klik untuk upload atau drag & drop</p>
                            <small>Format: JPEG, PNG, GIF | Maksimal: 5MB</small>
                        </div>
                        <img id="image-preview" src="" alt="Preview" class="image-preview" style="display: none;">
                    </div>
                    @error('image')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h3>Publikasi</h3>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                        <span>Publikasikan artwork sekarang</span>
                    </label>
                    <small class="muted-text">Artwork yang dipublikasikan akan terlihat di halaman portfolio publik</small>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.artworks.index') }}" class="button button-outline">Batal</a>
                <button type="submit" class="button button-primary">Simpan Artwork</button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
