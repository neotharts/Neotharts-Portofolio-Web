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
                    <label for="sort_order">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0" step="1" placeholder="0">
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
                    <span class="form-label">Preview Tag</span>
                    <div class="tag-preview">
                        <span id="type-tag" class="type-tag tag-{{ old('type') ?? '' }}">{{ old('type') ? ucfirst(old('type')) : '' }}</span>
                        <span id="service-preview" class="type-tag"></span>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3>Gambar</h3>

                <div class="form-group">
                    <label for="images">Upload Gambar *</label>
                    <div class="file-upload" id="fileUploadContainer">
                        <input type="file" name="images[]" id="images" accept="image/*" required multiple onchange="previewImages(event)">
                        <label for="images" class="file-upload-area">
                            <span class="material-icons-outlined">cloud_upload</span>
                            <p>Klik untuk upload atau drag & drop</p>
                            <small>Format: JPEG, PNG, GIF, WebP | Bisa pilih beberapa gambar | Maksimal 12 gambar</small>
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

                <!-- Image Order Hidden Input -->
                <input type="hidden" name="image_order" id="imageOrder" value="">
                <p class="image-order-hint"><span class="material-icons-outlined">info</span> Drag gambar untuk mengubah urutan carousel</p>
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

        .image-preview-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 12px;
        }

        .image-preview-list img {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 18px;
            object-fit: cover;
            border: 1px solid rgba(31, 27, 24, 0.08);
        }

        /* Drag and Drop Image Reordering */
        .image-preview-list.dragging {
            cursor: grabbing;
        }

        .image-preview-list.drag-over {
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
    </style>

    <script>
        // Disable form caching
        if (window.history.replaceState) {
            // Clear form data on page load
            window.addEventListener('load', function() {
                // Clear any cached form data
                setTimeout(function() {
                    // Force clear file input
                    const fileInput = document.getElementById('images');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                }, 100);
            });
        }

        const fileInput = document.getElementById('images');
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

        function previewImages(event) {
            const files = Array.from(event.target.files);
            const previewList = document.getElementById('image-preview-list');

            previewList.innerHTML = '';

            if (files.length > 0) {
                // Store files for cropping
                window.uploadFiles = files;

                files.forEach((file, index) => {
                    const item = document.createElement('div');
                    item.className = 'preview-item';
                    item.setAttribute('draggable', 'true');
                    item.setAttribute('data-index', index);
                    item.setAttribute('data-original-name', file.name);

                    const image = document.createElement('img');
                    image.alt = file.name;

                    const dragHandle = document.createElement('div');
                    dragHandle.className = 'drag-handle';
                    dragHandle.innerHTML = '<span class="material-icons-outlined" style="font-size:16px">drag_indicator</span>';

                    const orderBadge = document.createElement('div');
                    orderBadge.className = 'order-badge';
                    orderBadge.textContent = index + 1;

                    // Crop button
                    const cropBtn = document.createElement('button');
                    cropBtn.type = 'button';
                    cropBtn.className = 'crop-btn';
                    cropBtn.title = 'Crop 4:5';
                    cropBtn.innerHTML = '<span class="material-icons-outlined">crop</span>';
                    cropBtn.onclick = function(e) {
                        e.stopPropagation();
                        initCrop(file, null, item, index);
                    };

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        image.src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    item.appendChild(image);
                    item.appendChild(dragHandle);
                    item.appendChild(orderBadge);
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
            const previewList = document.getElementById('image-preview-list');
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

            // Reorder window.imageFileOrder
            if (window.imageFileOrder) {
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

            // Update hidden input
            updateImageOrderInput();
        }

        function updateImageOrderInput() {
            const previewList = document.getElementById('image-preview-list');
            const items = previewList.querySelectorAll('.preview-item');
            const order = Array.from(items).map(item => item.getAttribute('data-original-name') || item.querySelector('img').alt);
            document.getElementById('imageOrder').value = JSON.stringify(order);
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

        // Form submit handler
        document.querySelector('.artwork-form').addEventListener('submit', function(e) {
            console.log('Form submitting...');
            console.log('Title:', document.getElementById('title').value);
            console.log('Type:', document.getElementById('type').value);
            console.log('Image:', document.getElementById('images').files.length);
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
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
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
            transform: translate(-50%, -50%) scale(1.1);
        }

        .preview-item .crop-btn .material-icons-outlined {
            font-size: 20px;
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
    </style>

    <script>
        // Store files for cropping
        window.uploadFiles = [];
        let cropper = null;
        let currentCropItem = null;

        // Initialize crop functionality
        function initCrop(file, callback, item, fileIndex) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const cropImage = document.getElementById('cropImage');
                cropImage.src = e.target.result;

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

                // Store current crop context
                currentCropItem = { file, callback, item, fileIndex };
            };
            reader.readAsDataURL(file);
        }

        // Crop and save
        function cropImage() {
            if (!cropper || !currentCropItem) return;

            const canvas = cropper.getCroppedCanvas({
                width: 960,
                height: 1200,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            canvas.toBlob(function(blob) {
                const croppedFile = new File([blob], currentCropItem.file.name, {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });

                // Update file in storage
                window.uploadFiles[currentCropItem.fileIndex] = croppedFile;

                // Update preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    currentCropItem.item.querySelector('img').src = e.target.result;
                    currentCropItem.item.classList.add('cropped');
                };
                reader.readAsDataURL(croppedFile);

                // Update file input
                updateFileInput();

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
        }

        // Update file input with stored files
        function updateFileInput() {
            const dt = new DataTransfer();
            window.uploadFiles.forEach(file => {
                if (file) dt.items.add(file);
            });
            document.getElementById('images').files = dt.files;
        }
    </script>
@endsection
