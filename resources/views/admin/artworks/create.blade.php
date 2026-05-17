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
                    <input type="text" name="title" id="title" value="{{ old('title') }}" placeholder="Masukkan judul artwork" autocomplete="off" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi *</label>
                    <textarea name="description" id="description" placeholder="Jelaskan tentang artwork Anda..." rows="6" autocomplete="off" required>{{ old('description') }}</textarea>
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
                        <select name="type" id="type" required onchange="updateTypeDropdown()">
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
                        <label for="art_for">Art For (Untuk siapa) *</label>
                        <input type="text" name="art_for" id="art_for" value="{{ old('art_for', 'myself') }}" placeholder="Nama client atau 'myself'" autocomplete="off" required>
                        <small class="muted-text">Nama client/untuk siapa karya ini. Kosong = "myself"</small>
                        @error('art_for')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>List Service</label>
                    <p class="form-label">Pilih satu atau lebih service yang tersedia untuk artwork ini:</p>
                    <div class="service-checkbox-group">
                        @foreach($availableServices as $service)
                            <label class="service-checkbox-item">
                                <input type="checkbox" name="list_service[]" value="{{ $service }}"
                                    {{ is_array(old('list_service')) && in_array($service, old('list_service')) ? 'checked' : '' }}>
                                <span class="service-checkbox-label tag-{{ $service }}">{{ ucfirst(str_replace('-', ' ', $service)) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('list_service')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Preview Tag</label>
                    <div class="tag-preview">
                        <span id="type-tag" class="type-tag tag-{{ old('type') ?? '' }}">{{ old('type') ? ucfirst(old('type')) : '' }}</span>
                        <span id="service-preview" class="type-tag"></span>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Gambar</h3>

                <div class="form-group">
                    <label for="image">Upload Gambar *</label>
                    <div class="file-upload" id="fileUploadContainer">
                        <input type="file" name="image" id="image" accept="image/*" required onchange="previewImage(event)">
                        <label for="image" class="file-upload-area">
                            <span class="material-icons-outlined">cloud_upload</span>
                            <p>Klik untuk upload atau drag & drop</p>
                            <small>Format: JPEG, PNG, GIF, WebP | Akan otomatis di-compress</small>
                        </label>
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

    <style>
        .service-checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
        }

        .service-checkbox-item {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .service-checkbox-item input[type="checkbox"] {
            display: none;
        }

        .service-checkbox-label {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
            background: var(--bg-tertiary, #f0f0f0);
            color: var(--text-muted, #666);
            border: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .service-checkbox-item input[type="checkbox"]:checked + .service-checkbox-label {
            background: var(--accent-color, #e8a87c);
            color: white;
            border-color: var(--accent-color, #e8a87c);
        }

        .service-checkbox-item:hover .service-checkbox-label {
            border-color: var(--accent-color, #e8a87c);
        }

        /* Tag colors for each service */
        .tag-headshot { --tag-color: #e8a87c; }
        .tag-halfbody { --tag-color: #c38c9c; }
        .tag-fullbody { --tag-color: #85c1ae; }
        .tag-chibi { --tag-color: #9bc1e8; }

        .service-checkbox-item input[type="checkbox"]:checked + .tag-headshot { background: #e8a87c; border-color: #e8a87c; }
        .service-checkbox-item input[type="checkbox"]:checked + .tag-halfbody { background: #c38c9c; border-color: #c38c9c; }
        .service-checkbox-item input[type="checkbox"]:checked + .tag-fullbody { background: #85c1ae; border-color: #85c1ae; }
        .service-checkbox-item input[type="checkbox"]:checked + .tag-chibi { background: #9bc1e8; border-color: #9bc1e8; }
    </style>

    <script>
        const fileInput = document.getElementById('image');
        const uploadArea = document.querySelector('.file-upload-area');
        const uploadContainer = document.getElementById('fileUploadContainer');

        // Click to upload
        uploadArea.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.click();
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    uploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        // Services data grouped by type
        const servicesByType = @json($servicesByType ?? collect([]));

        // Available services
        const availableServices = @json($availableServices ?? []);

        function updateTypeDropdown() {
            const typeSelect = document.getElementById('type');
            const selectedType = typeSelect.value;

            // Update type tag
            const typeTag = document.getElementById('type-tag');
            if (selectedType) {
                typeTag.textContent = selectedType.charAt(0).toUpperCase() + selectedType.slice(1);
                typeTag.className = 'type-tag tag-' + selectedType;
            } else {
                typeTag.textContent = '';
                typeTag.className = 'type-tag';
            }
        }

        // Update service preview when checkboxes change
        function updateServicePreview() {
            const checkboxes = document.querySelectorAll('input[name="list_service[]"]:checked');
            const previewSpan = document.getElementById('service-preview');

            if (checkboxes.length > 0) {
                const selectedServices = Array.from(checkboxes).map(cb => {
                    const value = cb.value;
                    return value.charAt(0).toUpperCase() + value.slice(1);
                });
                previewSpan.textContent = selectedServices.join(', ');
                previewSpan.style.display = 'inline-flex';
            } else {
                previewSpan.textContent = '';
                previewSpan.style.display = 'none';
            }
        }

        // Listen for checkbox changes
        document.querySelectorAll('input[name="list_service[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', updateServicePreview);
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            if (typeSelect.value) {
                updateTypeDropdown();
            }
            updateServicePreview();
        });
    </script>
@endsection