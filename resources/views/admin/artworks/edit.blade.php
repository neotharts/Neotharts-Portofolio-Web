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

        <form action="{{ route('admin.artworks.update', $artwork) }}" method="POST" enctype="multipart/form-data" class="artwork-form" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Informasi Dasar</h3>

                <div class="form-group">
                    <label for="title">Judul Artwork *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $artwork->title) }}" placeholder="Masukkan judul artwork" autocomplete="off" required>
                    @error('title')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi *</label>
                    <textarea name="description" id="description" placeholder="Jelaskan tentang artwork Anda..." rows="6" autocomplete="off" required>{{ old('description', $artwork->description) }}</textarea>
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
                        <label for="art_for">Art For (Untuk siapa) *</label>
                        <input type="text" name="art_for" id="art_for" value="{{ old('art_for', $artwork->art_for ?? 'myself') }}" placeholder="Nama client atau 'myself'" autocomplete="off" required>
                        <small class="muted-text">Nama client/untuk siapa karya ini. Kosong = "myself"</small>
                        @error('art_for')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="sort_order">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $artwork->sort_order ?? 0) }}" min="0" step="1" placeholder="0" autocomplete="off">
                    <small class="muted-text">Angka lebih kecil akan tampil lebih dulu. Jika sama, artwork terbaru tampil lebih dulu.</small>
                    @error('sort_order')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <span class="form-label">List Service</span>
                    <p class="form-description">Pilih satu atau lebih service yang tersedia untuk artwork ini:</p>
                    <div class="service-checkbox-group" role="group" aria-label="Pilih Service">
                        @foreach($availableServices as $index => $service)
                            <label class="service-checkbox-item" for="list_service_{{ $index }}">
                                <input type="checkbox" id="list_service_{{ $index }}" name="list_service[]" value="{{ $service }}"
                                    {{ is_array(old('list_service', $artwork->list_service ?? [])) && in_array($service, old('list_service', $artwork->list_service ?? [])) ? 'checked' : '' }}>
                                <span class="service-checkbox-label tag-{{ $service }}">{{ ucfirst(str_replace('-', ' ', $service)) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('list_service')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <span class="form-label">Preview Tag</span>
                    <div class="tag-preview">
                        <span id="type-tag" class="type-tag tag-{{ $artwork->type ?? '' }}">{{ isset($artwork->type) ? ucfirst($artwork->type) : '' }}</span>
                        <span id="service-preview" class="type-tag"></span>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Gambar</h3>

                @if(count($artwork->gallery_images) > 0)
                    <div class="current-image">
                        <p class="form-label">Gambar Saat Ini ({{ count($artwork->gallery_images) }}/12)</p>
                        <div class="current-image-grid" id="currentImageGrid">
                            @foreach($artwork->gallery_images as $index => $image)
                                <div class="preview-item existing-image" draggable="true" data-index="{{ $index }}" data-original-name="{{ basename($image) }}" data-image-path="{{ $image }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $artwork->title }}" class="current-image-preview">
                                    <div class="drag-handle">
                                        <span class="material-icons-outlined" style="font-size:16px">drag_indicator</span>
                                    </div>
                                    <div class="order-badge">{{ $index + 1 }}</div>
                                    <button type="button" class="delete-image-btn" onclick="removeImage(this)" title="Hapus gambar">
                                        <span class="material-icons-outlined" style="font-size:16px">close</span>
                                    </button>
                                    <button type="button" class="crop-btn" onclick="cropExistingImage(this, '{{ $image }}', {{ $index }})" title="Crop 4:5">
                                        <span class="material-icons-outlined">crop</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <p class="image-order-hint"><span class="material-icons-outlined">info</span> Drag gambar untuk mengubah urutan carousel. Maksimal 12 gambar.</p>
                    </div>
                @endif

                <div class="form-group" id="addImagesGroup" style="{{ count($artwork->gallery_images) >= 12 ? 'display:none' : '' }}">
                    <label for="add_images">Tambah Gambar</label>
                    <div class="file-upload" id="addImagesContainer">
                        <input type="file" name="add_images[]" id="add_images" accept="image/*" multiple onchange="previewAddImages(event)" {{ count($artwork->gallery_images) >= 12 ? 'disabled' : '' }}>
                        <label for="add_images" class="file-upload-area">
                            <span class="material-icons-outlined">add_photo_alternate</span>
                            <p>Tambah gambar ke carousel</p>
                            <small>{{ 12 - count($artwork->gallery_images) }} slot tersisa</small>
                        </label>
                        <div id="add-images-preview-list" class="image-preview-list" style="display: none;"></div>
                    </div>
                    @error('add_images')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @error('add_images.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" id="replaceImagesGroup" style="{{ count($artwork->gallery_images) >= 12 ? 'display:none' : '' }}">
                    <label for="images">Ganti Semua Gambar</label>
                    <div class="file-upload" id="fileUploadContainer">
                        <input type="file" name="images[]" id="images" accept="image/*" multiple onchange="previewImages(event)" {{ count($artwork->gallery_images) >= 12 ? 'disabled' : '' }}>
                        <label for="images" class="file-upload-area">
                            <span class="material-icons-outlined">cloud_upload</span>
                            <p>Ganti semua gambar sekaligus</p>
                            <small>Ini akan menghapus dan mengganti semua gambar yang ada</small>
                        </label>
                        <div id="image-preview-list" class="image-preview-list" style="display: none;"></div>
                    </div>
                    @error('images')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    @error('images.*')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Hidden field for image order -->
                <input type="hidden" name="image_order" id="imageOrder" value="">
                <input type="hidden" name="images_to_delete" id="imagesToDelete" value="[]">
                <input type="hidden" name="cropped_originals" id="croppedOriginals" value="[]">
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
                <button type="submit" class="button button-primary artwork-submit-button" data-loading-text="Mengupload...">Update Artwork</button>
            </div>
        </form>

        <div class="upload-loading-overlay" id="uploadLoadingOverlay" role="status" aria-live="polite" aria-hidden="true">
            <div class="upload-loading-panel">
                <span class="upload-spinner" aria-hidden="true"></span>
                <div>
                    <p class="upload-loading-title">Memproses artwork</p>
                    <p class="upload-loading-text">Perubahan sedang disimpan. Gambar baru akan dikirim dan dikompres terlebih dahulu.</p>
                    <div class="upload-progress-bar" aria-hidden="true">
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
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

        .current-image-grid,
        .image-preview-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
        }

        .current-image-grid img,
        .image-preview-list img {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid rgba(31, 27, 24, 0.08);
        }

        /* Drag and Drop Image Reordering */
        .image-preview-list.dragging,
        .current-image-grid.dragging {
            cursor: grabbing;
        }

        .image-preview-list.drag-over,
        .current-image-grid.drag-over {
            outline: 2px dashed var(--accent-color, #e8a87c);
            outline-offset: 4px;
        }

        .preview-item {
            position: relative;
            cursor: grab;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .preview-item:hover {
            transform: scale(1.02);
        }

        .preview-item.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }

        .preview-item .drag-handle {
            position: absolute;
            top: 4px;
            left: 4px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 10;
            cursor: grab;
        }

        .preview-item:hover .drag-handle {
            opacity: 1;
        }

        .preview-item .order-badge {
            position: absolute;
            bottom: 4px;
            right: 4px;
            background: var(--accent-color, #e8a87c);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .image-order-hint {
            font-size: 12px;
            color: var(--text-muted, #666);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .image-order-hint .material-icons-outlined {
            font-size: 16px;
        }

        /* Delete Image Button */
        .preview-item .delete-image-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease, background 0.2s ease;
            z-index: 10;
            cursor: pointer;
            border: none;
            padding: 0;
        }

        .preview-item:hover .delete-image-btn {
            opacity: 1;
        }

        .preview-item .delete-image-btn:hover {
            background: rgba(220, 53, 69, 1);
            transform: scale(1.1);
        }

        .preview-item.removing {
            opacity: 0.3;
            transform: scale(0.8);
        }

        .file-upload-area .material-icons-outlined {
            font-size: 48px;
            color: var(--text-muted, #666);
        }

        .file-upload-area:hover .material-icons-outlined {
            color: var(--accent-color, #e8a87c);
        }

        #replaceImagesGroup .file-upload-area {
            border-color: #dc3545;
            background: rgba(220, 53, 69, 0.05);
        }

        #replaceImagesGroup .file-upload-area:hover {
            background: rgba(220, 53, 69, 0.1);
        }

        #replaceImagesGroup .file-upload-area small {
            color: #dc3545;
        }

        #addImagesGroup .file-upload-area .material-icons-outlined {
            color: var(--accent-color, #e8a87c);
        }

        .upload-loading-overlay {
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 15, 22, 0.58);
            backdrop-filter: blur(8px);
        }

        .upload-loading-overlay.is-visible {
            display: flex;
        }

        .upload-loading-panel {
            width: min(420px, 100%);
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 20px;
            border-radius: 14px;
            background: var(--card-bg, #fff);
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.28);
        }

        .upload-spinner {
            flex: 0 0 auto;
            width: 34px;
            height: 34px;
            border: 3px solid rgba(232, 168, 124, 0.25);
            border-top-color: var(--accent-color, #e8a87c);
            border-radius: 50%;
            animation: upload-spin 0.8s linear infinite;
        }

        .upload-loading-title {
            margin: 0 0 4px;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-color, #241f1b);
        }

        .upload-loading-text {
            margin: 0;
            font-size: 13px;
            color: var(--text-muted, #666);
            line-height: 1.5;
        }

        .upload-progress-bar {
            position: relative;
            height: 6px;
            margin-top: 14px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(232, 168, 124, 0.18);
        }

        .upload-progress-bar span {
            position: absolute;
            inset: 0 auto 0 0;
            width: 42%;
            border-radius: inherit;
            background: var(--accent-color, #e8a87c);
            animation: upload-progress 1.1s ease-in-out infinite;
        }

        .artwork-submit-button.is-loading {
            pointer-events: none;
            opacity: 0.78;
        }

        .artwork-submit-button.is-loading::before {
            content: '';
            display: inline-block;
            width: 14px;
            height: 14px;
            margin-right: 8px;
            vertical-align: -2px;
            border: 2px solid rgba(255, 255, 255, 0.45);
            border-top-color: #fff;
            border-radius: 50%;
            animation: upload-spin 0.8s linear infinite;
        }

        @keyframes upload-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes upload-progress {
            0% {
                transform: translateX(-120%);
            }

            100% {
                transform: translateX(240%);
            }
        }
    </style>

    <script>
        // Initialize image order for existing images
        window.existingImageOrder = @json($artwork->gallery_images ?? []);

        function setupDragDrop(uploadArea, fileInput, previewCallback) {
            // Click to upload
            uploadArea.addEventListener('click', function(e) {
                e.preventDefault();
                if (!fileInput.disabled) fileInput.click();
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
                if (files.length > 0 && !fileInput.disabled) {
                    fileInput.files = files;
                    fileInput.dispatchEvent(new Event('change'));
                }
            });
        }

        // Setup drag/drop for replace images section
        const replaceUploadArea = document.querySelector('#fileUploadContainer .file-upload-area');
        const fileInput = document.getElementById('images');
        if (replaceUploadArea) {
            setupDragDrop(replaceUploadArea, fileInput, 'previewImages');
        }

        // Setup drag/drop for add images section
        const addImagesUploadArea = document.querySelector('#addImagesContainer .file-upload-area');
        const addImagesInput = document.getElementById('add_images');
        if (addImagesUploadArea) {
            setupDragDrop(addImagesUploadArea, addImagesInput, 'previewAddImages');
        }

        function previewImages(event) {
            const files = Array.from(event.target.files);
            const previewList = document.getElementById('image-preview-list');

            previewList.innerHTML = '';

            if (files.length > 0) {
                // Store files for cropping
                if (!window.replaceImagesFiles) window.replaceImagesFiles = [];
                files.forEach(f => window.replaceImagesFiles.push(f));

                files.forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'preview-item new-image';
                    item.setAttribute('draggable', 'true');
                    item.setAttribute('data-index', index);
                    item.setAttribute('data-original-name', file.name);
                    item.dataset.fileIndex = window.replaceImagesFiles.length - files.length + index;

                    const image = document.createElement('img');
                    image.alt = file.name;

                    const dragHandle = document.createElement('div');
                    dragHandle.className = 'drag-handle';
                    dragHandle.innerHTML = '<span class="material-icons-outlined" style="font-size:16px">drag_indicator</span>';

                    const orderBadge = document.createElement('div');
                    orderBadge.className = 'order-badge';
                    orderBadge.textContent = index + 1;

                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'delete-image-btn';
                    deleteBtn.onclick = function() { removeNewImage(this, 'image-preview-list'); };
                    deleteBtn.innerHTML = '<span class="material-icons-outlined" style="font-size:16px">close</span>';

                    // Crop button
                    const cropBtn = document.createElement('button');
                    cropBtn.type = 'button';
                    cropBtn.className = 'crop-btn';
                    cropBtn.title = 'Crop 4:5';
                    cropBtn.innerHTML = '<span class="material-icons-outlined">crop</span>';
                    cropBtn.onclick = function(e) {
                        e.stopPropagation();
                        const fileIdx = item.dataset.fileIndex;
                        const fileToCrop = window.replaceImagesFiles[fileIdx];
                        if (fileToCrop) {
                            currentCropItem = { element: item, name: fileToCrop.name, index: index, fileIndex: fileIdx };
                            initCrop(fileToCrop, function(croppedFile, pid) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    image.src = e.target.result;
                                    item.classList.add('cropped');
                                };
                                reader.readAsDataURL(croppedFile);
                                item.dataset.croppedFile = croppedFile;
                                window.replaceImagesFiles[fileIdx] = croppedFile;
                                updateFileInputs();
                            }, 'image-preview-list');
                        }
                    };

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        image.src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    item.appendChild(image);
                    item.appendChild(dragHandle);
                    item.appendChild(orderBadge);
                    item.appendChild(deleteBtn);
                    item.appendChild(cropBtn);
                    previewList.appendChild(item);

                    // Add drag events
                    item.addEventListener('dragstart', handleDragStart);
                    item.addEventListener('dragend', handleDragEnd);
                    item.addEventListener('dragover', handleDragOver);
                    item.addEventListener('drop', handleDrop);
                    item.addEventListener('dragleave', handleDragLeave);
                });

                previewList.style.display = 'grid';
                replaceUploadArea.style.display = 'none';

                // Store file order
                window.imageFileOrder = files;
            }
        }

        // Drag and Drop Functions
        let draggedItem = null;
        let draggedIndex = null;

        function handleDragStart(e) {
            draggedItem = this;
            draggedIndex = parseInt(this.getAttribute('data-index'));
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', draggedIndex);
        }

        function handleDragEnd(e) {
            this.classList.remove('dragging');
            draggedItem = null;
            draggedIndex = null;
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            this.classList.remove('drag-over');
        }

        function handleDrop(e) {
            e.preventDefault();
            this.classList.remove('drag-over');

            if (draggedItem && draggedItem !== this) {
                const targetIndex = parseInt(this.getAttribute('data-index'));
                reorderImages(draggedIndex, targetIndex);
            }
        }

        function reorderImages(fromIndex, toIndex) {
            const previewList = document.getElementById('currentImageGrid') || document.getElementById('image-preview-list');
            const items = Array.from(previewList.querySelectorAll('.preview-item'));

            if (fromIndex < toIndex) {
                const item = items[fromIndex];
                const itemsToMove = items.slice(fromIndex + 1, toIndex + 1);
                itemsToMove.forEach(el => el.parentNode.insertBefore(el, item));
            } else if (fromIndex > toIndex) {
                const item = items[fromIndex];
                const parent = item.parentNode;
                parent.insertBefore(item, items[toIndex]);
            }

            // Reorder window.existingImageOrder or window.imageFileOrder
            const isExistingImages = previewList.id === 'currentImageGrid';
            if (isExistingImages && window.existingImageOrder) {
                const [removed] = window.existingImageOrder.splice(fromIndex, 1);
                window.existingImageOrder.splice(toIndex, 0, removed);
            } else if (window.imageFileOrder) {
                const [removed] = window.imageFileOrder.splice(fromIndex, 1);
                window.imageFileOrder.splice(toIndex, 0, removed);
            }

            // Update data-index and order badges
            const updatedItems = Array.from(previewList.querySelectorAll('.preview-item'));
            updatedItems.forEach((item, index) => {
                item.setAttribute('data-index', index);
                const badge = item.querySelector('.order-badge');
                if (badge) {
                    badge.textContent = index + 1;
                }
            });

            // Update hidden inputs
            updateImageOrderInput();
        }

        function updateImageOrderInput() {
            // Get all preview containers
            const currentGrid = document.getElementById('currentImageGrid');
            const replacePreview = document.getElementById('image-preview-list');
            const addPreview = document.getElementById('add-images-preview-list');

            let allImages = [];

            // If there's a "replace all" preview, use that (it replaces everything)
            if (replacePreview && replacePreview.querySelectorAll('.preview-item').length > 0) {
                const items = replacePreview.querySelectorAll('.preview-item');
                items.forEach(item => {
                    allImages.push({
                        type: 'new',
                        name: item.getAttribute('data-original-name')
                    });
                });
            }
            // Otherwise, combine existing images and add images
            else {
                // Add existing images (from currentImageGrid)
                if (currentGrid) {
                    const existingItems = currentGrid.querySelectorAll('.preview-item.existing-image');
                    existingItems.forEach(item => {
                        allImages.push({
                            type: 'existing',
                            path: item.getAttribute('data-image-path')
                        });
                    });
                }

                // Add new images (from add-images-preview-list)
                if (addPreview) {
                    const newItems = addPreview.querySelectorAll('.preview-item');
                    newItems.forEach(item => {
                        allImages.push({
                            type: 'new',
                            name: item.getAttribute('data-original-name')
                        });
                    });
                }
            }

            document.getElementById('imageOrder').value = JSON.stringify(allImages);
            console.log('Image order updated:', allImages);
        }

        // Remove image functionality
        function removeImage(btn) {
            const item = btn.closest('.preview-item');
            const imagePath = item.getAttribute('data-image-path');

            // Add to images to delete list
            let imagesToDelete = JSON.parse(document.getElementById('imagesToDelete').value || '[]');
            if (!imagesToDelete.includes(imagePath)) {
                imagesToDelete.push(imagePath);
                document.getElementById('imagesToDelete').value = JSON.stringify(imagesToDelete);
            }

            // Remove from existingImageOrder
            if (window.existingImageOrder) {
                const index = window.existingImageOrder.indexOf(imagePath);
                if (index > -1) {
                    window.existingImageOrder.splice(index, 1);
                }
            }

            // Animate removal
            item.classList.add('removing');
            setTimeout(() => {
                item.remove();
                // Update order badges for remaining items
                updateOrderBadges();
                updateImageOrderInput();
                updateSlotInfo();
            }, 200);
        }

        function updateOrderBadges() {
            const currentGrid = document.getElementById('currentImageGrid');
            if (currentGrid) {
                const items = currentGrid.querySelectorAll('.preview-item');
                items.forEach((item, index) => {
                    item.setAttribute('data-index', index);
                    const badge = item.querySelector('.order-badge');
                    if (badge) {
                        badge.textContent = index + 1;
                    }
                });
            }
        }

        function updateSlotInfo() {
            const currentGrid = document.getElementById('currentImageGrid');
            const addImagesInput = document.getElementById('add_images');
            const replaceImagesInput = document.getElementById('images');
            const addImagesLabel = document.querySelector('#addImagesGroup label');
            const replaceImagesLabel = document.querySelector('#replaceImagesGroup label');
            const addImagesSmall = document.querySelector('#addImagesGroup .file-upload-area small');
            const replaceImagesSmall = document.querySelector('#replaceImagesGroup .file-upload-area small');

            if (currentGrid) {
                const currentCount = currentGrid.querySelectorAll('.preview-item.existing-image').length;
                const remainingSlots = 12 - currentCount;

                // Update label counts
                const currentLabel = currentGrid.closest('.current-image')?.querySelector('.form-label');
                if (currentLabel) {
                    currentLabel.textContent = `Gambar Saat Ini (${currentCount}/12)`;
                }

                if (remainingSlots <= 0) {
                    // Disable both upload options
                    if (addImagesInput) addImagesInput.disabled = true;
                    if (replaceImagesInput) replaceImagesInput.disabled = true;
                    document.getElementById('addImagesGroup').style.display = 'none';
                    document.getElementById('replaceImagesGroup').style.display = 'none';
                } else {
                    if (addImagesSmall) addImagesSmall.textContent = `${remainingSlots} slot tersisa`;
                }
            }
        }

        // Preview for "Add Images" (new images to append)
        function previewAddImages(event) {
            const files = Array.from(event.target.files);
            const previewList = document.getElementById('add-images-preview-list');
            const uploadArea = document.querySelector('#addImagesContainer .file-upload-area');

            previewList.innerHTML = '';

            const currentGrid = document.getElementById('currentImageGrid');
            const currentCount = currentGrid ? currentGrid.querySelectorAll('.preview-item.existing-image').length : 0;

            if (files.length > 0 && (currentCount + files.length) <= 12) {
                // Store files for cropping
                if (!window.addImagesFiles) window.addImagesFiles = [];
                files.forEach(f => window.addImagesFiles.push(f));

                files.forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'preview-item';
                    item.setAttribute('draggable', 'true');
                    item.setAttribute('data-index', currentCount + index);
                    item.setAttribute('data-original-name', file.name);
                    item.dataset.fileIndex = window.addImagesFiles.length - files.length + index;

                    const image = document.createElement('img');
                    image.alt = file.name;

                    const dragHandle = document.createElement('div');
                    dragHandle.className = 'drag-handle';
                    dragHandle.innerHTML = '<span class="material-icons-outlined" style="font-size:16px">drag_indicator</span>';

                    const orderBadge = document.createElement('div');
                    orderBadge.className = 'order-badge';
                    orderBadge.textContent = currentCount + index + 1;

                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.className = 'delete-image-btn';
                    deleteBtn.onclick = function() { removeNewImage(this); };
                    deleteBtn.innerHTML = '<span class="material-icons-outlined" style="font-size:16px">close</span>';

                    // Crop button
                    const cropBtn = document.createElement('button');
                    cropBtn.type = 'button';
                    cropBtn.className = 'crop-btn';
                    cropBtn.title = 'Crop 4:5';
                    cropBtn.innerHTML = '<span class="material-icons-outlined">crop</span>';
                    cropBtn.onclick = function(e) {
                        e.stopPropagation();
                        const fileIdx = item.dataset.fileIndex;
                        const fileToCrop = window.addImagesFiles[fileIdx];
                        if (fileToCrop) {
                            currentCropItem = { element: item, name: fileToCrop.name, index: index, fileIndex: fileIdx };
                            initCrop(fileToCrop, function(croppedFile, pid) {
                                // Update preview image
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    image.src = e.target.result;
                                    item.classList.add('cropped');
                                };
                                reader.readAsDataURL(croppedFile);
                                item.dataset.croppedFile = croppedFile;
                                window.addImagesFiles[fileIdx] = croppedFile;
                                updateFileInputs();
                            }, 'add-images-preview-list');
                        }
                    };

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        image.src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    item.appendChild(image);
                    item.appendChild(dragHandle);
                    item.appendChild(orderBadge);
                    item.appendChild(deleteBtn);
                    item.appendChild(cropBtn);
                    previewList.appendChild(item);

                    // Add drag events
                    item.addEventListener('dragstart', handleDragStart);
                    item.addEventListener('dragend', handleDragEnd);
                    item.addEventListener('dragover', handleDragOver);
                    item.addEventListener('drop', handleDrop);
                    item.addEventListener('dragleave', handleDragLeave);
                });

                previewList.style.display = 'grid';
                uploadArea.style.display = 'none';

                // Update slot info
                updateSlotInfo();

                // Add to new images storage
                if (!window.newImages) window.newImages = [];
                window.newImages.push(...files);
            } else {
                alert('Tidak bisa menambahkan gambar. Total maksimal 12 gambar.');
                event.target.value = '';
            }
        }

        function removeNewImage(btn, previewListId) {
            const item = btn.closest('.preview-item');
            const index = parseInt(item.getAttribute('data-index'));
            const previewList = previewListId ? document.getElementById(previewListId) : null;

            item.classList.add('removing');
            setTimeout(() => {
                item.remove();
                if (previewListId === 'add-images-preview-list') {
                    updateAddImagesOrderBadges();
                } else if (previewListId === 'image-preview-list') {
                    updateReplaceImagesOrderBadges();
                }
                updateImageOrderInput();
            }, 200);
        }

        function updateAddImagesOrderBadges() {
            const previewList = document.getElementById('add-images-preview-list');
            if (previewList) {
                const currentGrid = document.getElementById('currentImageGrid');
                const existingCount = currentGrid ? currentGrid.querySelectorAll('.preview-item.existing-image').length : 0;

                const items = previewList.querySelectorAll('.preview-item');
                items.forEach((item, index) => {
                    item.setAttribute('data-index', index);
                    const badge = item.querySelector('.order-badge');
                    if (badge) {
                        badge.textContent = existingCount + index + 1;
                    }
                });
            }
        }

        function updateReplaceImagesOrderBadges() {
            const previewList = document.getElementById('image-preview-list');
            if (previewList) {
                const items = previewList.querySelectorAll('.preview-item');
                items.forEach((item, index) => {
                    item.setAttribute('data-index', index);
                    const badge = item.querySelector('.order-badge');
                    if (badge) {
                        badge.textContent = index + 1;
                    }
                });
            }
        }

        // Initialize drag events for existing images
        document.addEventListener('DOMContentLoaded', function() {
            const currentGrid = document.getElementById('currentImageGrid');
            if (currentGrid) {
                const items = currentGrid.querySelectorAll('.preview-item');
                items.forEach(item => {
                    item.addEventListener('dragstart', handleDragStart);
                    item.addEventListener('dragend', handleDragEnd);
                    item.addEventListener('dragover', handleDragOver);
                    item.addEventListener('drop', handleDrop);
                    item.addEventListener('dragleave', handleDragLeave);
                });
            }

            updateImageOrderInput();
            updateSlotInfo();
        });

        // Services data from controller (grouped by type)
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

    <!-- Cropper.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <!-- Crop Modal -->
    <div id="cropModal" class="crop-modal" style="display:none;">
        <div class="crop-modal-content">
            <div class="crop-modal-header">
                <h3>Crop Gambar (4:5)</h3>
                <button type="button" class="crop-close-btn" onclick="closeCropModal()">&times;</button>
            </div>
            <div class="crop-modal-body">
                <img id="cropImage" src="" alt="Crop preview">
            </div>
            <div class="crop-modal-footer">
                <button type="button" class="button button-outline" onclick="closeCropModal()">Batal</button>
                <button type="button" class="button button-primary" onclick="cropImage()">Potong & Simpan</button>
            </div>
        </div>
    </div>

    <style>
        /* Crop Modal Styles */
        .crop-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .crop-modal-content {
            background: var(--card-bg, #fff);
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .crop-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color, #e0e0e0);
        }

        .crop-modal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .crop-close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: var(--text-muted, #666);
            padding: 0;
            line-height: 1;
        }

        .crop-close-btn:hover {
            color: var(--text-color, #333);
        }

        .crop-modal-body {
            padding: 20px;
            max-height: 400px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f5f5f5;
        }

        .crop-modal-body img {
            max-width: 100%;
            max-height: 360px;
        }

        .crop-modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 20px;
            border-top: 1px solid var(--border-color, #e0e0e0);
        }

        /* Crop button on preview items */
        .preview-item .crop-btn {
            position: absolute;
            bottom: 36px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 20;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .preview-item:hover .crop-btn {
            display: flex;
            opacity: 1;
        }

        .preview-item .crop-btn:hover {
            background: rgba(0, 0, 0, 0.8);
            transform: translateX(-50%) scale(1.1);
        }

        .preview-item .crop-btn .material-icons-outlined {
            font-size: 18px;
        }

        /* Existing image crop button - shows on hover in bottom-left corner */
        .preview-item.existing-image .crop-btn {
            width: 28px;
            height: 28px;
            bottom: 36px;
            left: 4px;
            right: auto;
            top: auto;
            transform: none;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            opacity: 0;
        }

        .preview-item.existing-image:hover .crop-btn {
            opacity: 0.8;
        }

        .preview-item.existing-image .crop-btn:hover {
            transform: scale(1.1);
            opacity: 1;
        }

        /* Cropped indicator */
        .preview-item.cropped::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 4px;
            width: 12px;
            height: 12px;
            background: #28a745;
            border-radius: 50%;
            border: 2px solid white;
        }

        .preview-item.cropped .order-badge {
            background: #28a745;
        }

        /* Has-cropped indicator - shows image has been cropped */
        .preview-item.has-cropped::before {
            content: '✓';
            position: absolute;
            top: 4px;
            left: 4px;
            background: #28a745;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            z-index: 25;
        }
    </style>

    <script>
        // Cropper instance
        let cropper = null;
        let currentCropItem = null;
        let currentCropPreviewId = null;
        let pendingCropCallback = null;

        // Initialize crop functionality
        function initCrop(file, callback, previewId) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const cropImage = document.getElementById('cropImage');
                cropImage.src = e.target.result;

                currentCropCallback = callback;
                currentCropPreviewId = previewId;

                // Show modal
                const modal = document.getElementById('cropModal');
                modal.style.display = 'flex';

                // Initialize Cropper.js with 4:5 aspect ratio
                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(cropImage, {
                    aspectRatio: 4 / 5,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            };
            reader.readAsDataURL(file);
        }

        // Crop and save
        function cropImage() {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 960,  // 4 * 240
                height: 1200, // 5 * 240
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            // Convert to blob
            canvas.toBlob(function(blob) {
                // Create new file from cropped blob
                const newFile = new File([blob], currentCropItem.name, {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });

                // Replace the file in the preview
                if (currentCropCallback) {
                    currentCropCallback(newFile, currentCropPreviewId);
                }

                closeCropModal();
            }, 'image/jpeg', 0.9);
        }

        // Close crop modal
        function closeCropModal() {
            const modal = document.getElementById('cropModal');
            modal.style.display = 'none';

            if (cropper) {
                cropper.destroy();
                cropper = null;
            }

            currentCropItem = null;
            currentCropCallback = null;
            currentCropPreviewId = null;
        }

        // Add crop button to preview items
        function addCropButton(item, file, index, previewId) {
            const cropBtn = document.createElement('button');
            cropBtn.type = 'button';
            cropBtn.className = 'crop-btn';
            cropBtn.title = 'Crop 4:5';
            cropBtn.innerHTML = '<span class="material-icons-outlined">crop</span>';
            cropBtn.onclick = function(e) {
                e.stopPropagation();
                currentCropItem = { element: item, name: file.name, index: index };
                initCrop(file, function(croppedFile, pid) {
                    // Update preview image
                    const img = item.querySelector('img');
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        item.classList.add('cropped');
                    };
                    reader.readAsDataURL(croppedFile);

                    // Update the file in the input
                    updateCroppedFile(croppedFile, index, previewId);
                }, previewId);
            };
            item.appendChild(cropBtn);
        }

        // Update cropped file in the file input
        function updateCroppedFile(croppedFile, index, previewId) {
            const dt = new DataTransfer();

            if (previewId === 'add-images-preview-list') {
                const previewItems = document.querySelectorAll('#add-images-preview-list .preview-item');
                previewItems.forEach((item, i) => {
                    if (i === index) {
                        dt.items.add(croppedFile);
                    } else if (item.dataset.croppedFile) {
                        dt.items.add(item.dataset.croppedFile);
                    }
                });
                document.getElementById('add_images').files = dt.files;
            } else if (previewId === 'image-preview-list') {
                const previewItems = document.querySelectorAll('#image-preview-list .preview-item');
                previewItems.forEach((item, i) => {
                    if (i === index) {
                        dt.items.add(croppedFile);
                    }
                });
                document.getElementById('images').files = dt.files;
            }
        }

        // Update file inputs with cropped files
        function updateFileInputs() {
            // Update add_images input
            if (window.addImagesFiles && window.addImagesFiles.length > 0) {
                const dt1 = new DataTransfer();
                window.addImagesFiles.forEach(file => {
                    if (file) dt1.items.add(file);
                });
                document.getElementById('add_images').files = dt1.files;
            }

            // Update images input
            if (window.replaceImagesFiles && window.replaceImagesFiles.length > 0) {
                const dt2 = new DataTransfer();
                window.replaceImagesFiles.forEach(file => {
                    if (file) dt2.items.add(file);
                });
                document.getElementById('images').files = dt2.files;
            }
        }

        // Store references for cropping existing images
        window.croppedExistingImages = {};  // { originalPath: croppedFile }

        // Crop existing image (fetch from URL and crop)
        function cropExistingImage(btn, imagePath, index) {
            const item = btn.closest('.preview-item');
            const imgElement = item.querySelector('img');
            const imageUrl = imgElement.src;

            // Show modal
            const modal = document.getElementById('cropModal');
            modal.style.display = 'flex';

            // Show loading state
            const cropImage = document.getElementById('cropImage');
            cropImage.src = imageUrl;

            // Initialize Cropper.js with 4:5 aspect ratio
            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(cropImage, {
                aspectRatio: 4 / 5,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 1,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
            });

            // Store reference for crop - use imagePath as unique key
            window.croppingExistingPath = imagePath;
            window.croppingExistingIndex = index;
            window.croppingExistingItem = item;
        }

        // Override cropImage to handle existing images
        const originalCropImage = cropImage;
        function cropImage() {
            if (!cropper) return;

            // Check if this is an existing image crop
            if (window.croppingExistingPath) {
                // Crop existing image
                const canvas = cropper.getCroppedCanvas({
                    width: 960,
                    height: 1200,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });

                canvas.toBlob(function(blob) {
                    const croppedFile = new File([blob], 'cropped_' + Date.now() + '.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now()
                    });

                    // Store cropped file keyed by original path
                    window.croppedExistingImages[window.croppingExistingPath] = croppedFile;

                    // Mark the item as having a cropped version
                    window.croppingExistingItem.classList.add('has-cropped');
                    window.croppingExistingItem.dataset.croppedFile = 'true';

                    // Update preview with cropped image
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        window.croppingExistingItem.querySelector('img').src = e.target.result;
                        window.croppingExistingItem.classList.add('cropped');
                    };
                    reader.readAsDataURL(croppedFile);

                    closeCropModal();

                    // Reset cropping state
                    window.croppingExistingPath = null;
                    window.croppingExistingIndex = undefined;
                    window.croppingExistingItem = null;
                }, 'image/jpeg', 0.9);
            } else {
                // Normal crop (from new images)
                originalCropImage.call(this);
            }
        }

        function showUploadLoading(form) {
            if (form.dataset.submitting === 'true') {
                return false;
            }

            form.dataset.submitting = 'true';

            const submitButton = form.querySelector('.artwork-submit-button');
            const overlay = document.getElementById('uploadLoadingOverlay');

            if (submitButton) {
                submitButton.dataset.originalText = submitButton.textContent;
                submitButton.textContent = submitButton.dataset.loadingText || 'Mengupload...';
                submitButton.classList.add('is-loading');
                submitButton.disabled = true;
            }

            form.querySelectorAll('input, select, textarea, button').forEach(control => {
                if (control.type !== 'hidden') {
                    control.setAttribute('aria-disabled', 'true');
                }
            });

            if (overlay) {
                overlay.classList.add('is-visible');
                overlay.setAttribute('aria-hidden', 'false');
            }

            return true;
        }

        // Handle form submission - process cropped existing images
        document.querySelector('.artwork-form').addEventListener('submit', function(e) {
            if (!showUploadLoading(this)) {
                e.preventDefault();
                return;
            }

            // Process cropped existing images
            const croppedExisting = Object.keys(window.croppedExistingImages || {});
            if (croppedExisting.length > 0) {
                // Add cropped files to add_images input for upload
                const dt = new DataTransfer();

                // Add all cropped files
                Object.values(window.croppedExistingImages).forEach(file => {
                    dt.items.add(file);
                });

                // Get existing files from add_images
                const addImagesInput = document.getElementById('add_images');
                if (addImagesInput.files.length > 0) {
                    Array.from(addImagesInput.files).forEach(file => {
                        if (file) dt.items.add(file);
                    });
                }

                addImagesInput.files = dt.files;

                // Pass list of original paths that were cropped (for deletion)
                document.getElementById('croppedOriginals').value = JSON.stringify(croppedExisting);

                // Mark originals for deletion
                const imagesToDelete = JSON.parse(document.getElementById('imagesToDelete').value || '[]');
                croppedExisting.forEach(path => {
                    if (!imagesToDelete.includes(path)) {
                        imagesToDelete.push(path);
                    }
                });
                document.getElementById('imagesToDelete').value = JSON.stringify(imagesToDelete);
            }
        });
    </script>
@endsection
