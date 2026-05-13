@extends('admin.layout')

@section('pageTitle', 'Edit Artwork')

@section('content')
    <div class="form-card glass-card">
        <div class="form-header">
            <div>
                <p class="eyebrow">Edit Artwork</p>
                <h2>{{ $artwork->title }}</h2>
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

        <form action="{{ route('admin.artworks.update', $artwork) }}" method="POST" enctype="multipart/form-data" class="artwork-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Informasi Dasar</h3>

                <div class="form-group">
                    <label for="title">Judul Artwork *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $artwork->title) }}" placeholder="Masukkan judul artwork" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi *</label>
                    <textarea name="description" id="description" placeholder="Jelaskan tentang artwork Anda..." rows="6" required>{{ old('description', $artwork->description) }}</textarea>
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
                        <select name="type" id="type" required onchange="updateTypeTag(this.value)">
                            <option value="">Pilih tipe...</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ old('type', $artwork->type) === $type ? 'selected' : '' }}>
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
                        <select name="form" id="form" required onchange="updateFormTag(this.value)">
                            <option value="">Pilih form...</option>
                            @foreach($forms as $form)
                                <option value="{{ $form }}" {{ old('form', $artwork->form) === $form ? 'selected' : '' }}>
                                    {{ ucfirst($form) }}
                                </option>
                            @endforeach
                        </select>
                        @error('form')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="art_for">Art For (Untuk siapa) *</label>
                        <input type="text" name="art_for" id="art_for" value="{{ old('art_for', $artwork->art_for ?? 'myself') }}" placeholder="Nama client atau 'myself'" required>
                        <small class="muted-text">Nama client/untuk siapa karya ini. Kosong = "myself"</small>
                        @error('art_for')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Preview Tag</label>
                    <div class="tag-preview">
                        <span id="type-tag" class="type-tag tag-{{ $artwork->type ?? '' }}">{{ isset($artwork->type) ? ucfirst($artwork->type) : '' }}</span>
                        <span id="form-tag" class="type-tag tag-{{ $artwork->form ?? '' }}">{{ isset($artwork->form) ? ucfirst($artwork->form) : '' }}</span>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Gambar</h3>

                @if($artwork->image)
                    <div class="current-image">
                        <p class="form-label">Gambar Saat Ini</p>
                        <img src="{{ asset('storage/' . $artwork->image) }}" alt="{{ $artwork->title }}" class="current-image-preview">
                    </div>
                @endif

                <div class="form-group">
                    <label for="image">Update Gambar (Opsional)</label>
                    <div class="file-upload">
                        <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(event)">
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
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $artwork->is_published) ? 'checked' : '' }}>
                        <span>Publikasikan artwork</span>
                    </label>
                    <small class="muted-text">Artwork yang dipublikasikan akan terlihat di halaman portfolio publik</small>
                </div>

                @if($artwork->is_published)
                    <p class="muted-text">
                        <span class="material-icons-outlined">info</span>
                        Dipublikasikan pada: {{ $artwork->published_at?->format('d M Y H:i') }}
                    </p>
                @endif
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.artworks.index') }}" class="button button-outline">Batal</a>
                <button type="submit" class="button button-primary">Update Artwork</button>
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

        function updateTypeTag(value) {
            const tag = document.getElementById('type-tag');
            if (value) {
                tag.textContent = value.charAt(0).toUpperCase() + value.slice(1);
                tag.className = 'type-tag tag-' + value;
            } else {
                tag.textContent = '';
                tag.className = 'type-tag';
            }
        }

        function updateFormTag(value) {
            const tag = document.getElementById('form-tag');
            if (value) {
                tag.textContent = value.charAt(0).toUpperCase() + value.slice(1);
                tag.className = 'type-tag tag-' + value;
            } else {
                tag.textContent = '';
                tag.className = 'type-tag';
            }
        }
    </script>
@endsection
