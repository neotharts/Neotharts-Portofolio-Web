<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Commission</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite('resources/css/home.css')
    <link rel="stylesheet" href="{{ asset('css/commission.css') }}">
    <!-- Face API.js for face detection -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body>
    <nav>
        <div class="mainav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('artworks') }}">Artworks</a>
            <a href="{{ route('commission') }}" class="active">Commissions</a>
            <a href="{{ route('three_d') }}">3D</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <div class="mainavmobile">
            <span class="material-icons">menu</span>
        </div>
    </nav>
    @include('partials.mobile-fullscreen-nav')

    <section class="gallery-header">
        <div class="mainheaders">
            <img class="desktop" src="{{ asset('img/COMMISSION.png') }}" alt="Commission">
        </div>
    </section>

    <section class="coms_menu">
        <div class="coms_menu_container">
            <div class="filter-list">
                <a href="#commission-list" class="filter-item">Commission</a>
                <a href="#progressBoardModal" class="filter-item" id="openProgressBoard">Order list</a>
                <a href="#tosModal" class="filter-item">TOS</a>
            </div>
        </div>
    </section>

    @if($services->count() > 0)
    <section id="commission-list">
        <div class="commission-container">
            <div class="commission-cards">
                @foreach($services as $service)
                <div class="commission-card" onclick="showServiceArtworks('{{ $service->name }}', {{ $service->starting_price }}, {{ json_encode($service->features_array ?? []) }})">
                    <div class="card-image">
                        @if(isset($serviceLatestImagesArray[$service->id]) && $serviceLatestImagesArray[$service->id])
                            <img src="{{ asset('storage/' . $serviceLatestImagesArray[$service->id]['image']) }}"
                                 alt="{{ $service->name }}"
                                 class="smart-crop"
                                 data-service-id="{{ $service->id }}"
                                 data-service-name="{{ $service->name }}"
                                 data-full-url="{{ asset('storage/' . $serviceLatestImagesArray[$service->id]['image']) }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1618477461853-cf6ed80faba5?w=400&h=300&fit=crop" alt="{{ $service->name }}">
                        @endif
                    </div>
                    <div class="card-content">
                        <h3 class="card-title">{{ $service->name }}</h3>
                        <p class="card-price">Mulai dari <span class="price">$ {{ number_format($service->starting_price, 0, '.', ',') }}</span></p>
                        <button class="order-btn">
                            <span class="material-icons">shopping_cart</span>
                            Pesan Sekarang
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @php
        $statusLabels = \App\Models\Invoice::getStatusLabels();
        $statusColors = \App\Models\Invoice::getStatusColors();
    @endphp

    <div class="progress-board-modal-overlay" id="progressBoardModal">
        <div class="progress-board-modal-content" onclick="event.stopPropagation()">
            <div class="progress-board-header">
                <div>
                    <h2>Order list</h2>
                </div>
                <button class="modal-close-btn" onclick="closeProgressBoard()">×</button>
            </div>

            <div class="kanban-container">
            @foreach($statusLabels as $statusKey => $statusLabel)
                <div class="kanban-column">
                    <div class="kanban-column-header" style="background: {{ $statusColors[$statusKey] ?? '#ccc' }};">
                        <h3>{{ $statusLabel }}</h3>
                        <span>{{ $invoices->where('status', $statusKey)->count() }}</span>
                    </div>
                    <div class="kanban-column-body">
                        @forelse($invoices->where('status', $statusKey) as $invoice)
                            <div class="client-card readonly">
                                <div class="client-card-title">{{ $invoice->client_name ?: 'Client' }}</div>
                                <div class="client-card-subtitle">#{{ $invoice->invoice_number }}</div>
                                <div class="client-card-details">
                                    <span>{{ strtoupper($invoice->payment_method ?? '-') }}</span>
                                    <span>Rp {{ number_format($invoice->total_amount ?? 0, 0, ',', '.') }}</span>
                                </div>
                                @if($invoice->items->count())
                                    <div class="client-card-items">
                                        @foreach($invoice->items as $item)
                                            <div class="client-card-item">{{ $item->service_name ?? $item->service?->name ?? 'Service' }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="empty-column">Belum ada invoice</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
            </div>
        </div>
    </div>

    <!-- Service Artworks Gallery Modal -->
    <div class="modal-overlay" id="serviceModalOverlay" onclick="closeServiceModal(event)">
        <div class="modal-content service-modal-content" onclick="event.stopPropagation()">
            <div class="latestcoms">
                <img id="modalLatestImage" src="" alt="Latest artwork">
            </div>
            <div class="detailcoms">
                <div class="modal-header">
                    <h2 id="serviceModalTitle"></h2>
                    <button class="modal-close-btn" onclick="closeServiceModalDirect()">×</button>
                </div>
                <div class="pricecoms">
                    <p class="card-price">Mulai dari <span id="modalServicePrice" class="price">$ 0</span></p>
                </div>
                <div class="fiturcoms">
                    <h2>Fitur</h2>
                    <ul id="modalServiceFeatures">
                    </ul>
                </div>
                <div class="service-gallery-grid" id="serviceGalleryGrid">
                    <!-- Artworks will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- TOS Modal -->
    <div class="tos-modal-overlay" id="tosModal">
        <div class="tos-modal-content">
            <div class="tos-modal-header">
                <h2>Terms of Service</h2>
                <button class="tos-modal-close" onclick="closeTosModal()">×</button>
            </div>
            <div class="tos-modal-body">
                {!! $tosContent ?? '<p>Terms of Service belum tersedia.</p>' !!}
            </div>
        </div>
    </div>

    <script>
        // Store all artworks data
        const allArtworks = {!! json_encode($artworksArray ?? []) !!};

        console.log('All artworks loaded:', allArtworks.length);

        // Show artworks gallery for a service
        function showServiceArtworks(serviceName, price, features) {
            // Find all artworks with this service
            const artworks = allArtworks.filter(a => {
                if (!a.list_service || !Array.isArray(a.list_service)) return false;
                return a.list_service.some(s => s.toLowerCase() === serviceName.toLowerCase());
            });

            document.getElementById('serviceModalTitle').textContent = serviceName;

            // Set latest artwork image in modal
            const latestComsImg = document.getElementById('modalLatestImage');
            if (artworks.length > 0) {
                latestComsImg.src = '/storage/' + artworks[0].image;
                latestComsImg.style.display = 'block';
            } else {
                latestComsImg.style.display = 'none';
            }

            // Set price
            const formattedPrice = new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
                maximumFractionDigits: 0
            }).format(price || 0);
            document.getElementById('modalServicePrice').textContent = formattedPrice;

            // Set features
            const featuresList = document.getElementById('modalServiceFeatures');
            if (features && features.length > 0) {
                featuresList.innerHTML = features.map(f => '<li>' + f + '</li>').join('');
            } else {
                featuresList.innerHTML = '<li>Tidak ada fitur</li>';
            }

            const grid = document.getElementById('serviceGalleryGrid');
            if (artworks.length === 0) {
                grid.innerHTML = '<p class="no-artworks">No artworks found for this service</p>';
            } else {
                grid.innerHTML = artworks.map(artwork => `
                    <div class="service-artwork-item" onclick="openArtworkModal(${artwork.id})">
                        <img src="/storage/${artwork.image}" alt="${artwork.title}">
                        <div class="service-artwork-overlay">
                            <h4>${artwork.title}</h4>
                        </div>
                    </div>
                `).join('');
            }

            document.getElementById('serviceModalOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Open single artwork modal
        function openArtworkModal(id) {
            const artwork = allArtworks.find(a => a.id === id);
            if (!artwork) return;

            document.getElementById('modalImage').src = '/storage/' + artwork.image;
            document.getElementById('modalTitle').textContent = artwork.title || 'No Title';

            document.getElementById('modalType').textContent = (artwork.type || '').charAt(0).toUpperCase() + (artwork.type || '').slice(1);
            document.getElementById('modalType').className = 'modal-type tag-' + (artwork.type || '');

            const servicesContainer = document.getElementById('modalServices');
            if (artwork.list_service && artwork.list_service.length > 0) {
                const servicesHtml = artwork.list_service.map(service => {
                    const formattedService = service.charAt(0).toUpperCase() + service.slice(1);
                    return '<span class="modal-service-badge tag-' + service.toLowerCase() + '">' + formattedService + '</span>';
                }).join('');
                servicesContainer.innerHTML = servicesHtml;
                servicesContainer.style.display = 'flex';
            } else {
                servicesContainer.innerHTML = '';
                servicesContainer.style.display = 'none';
            }

            document.getElementById('modalDescription').textContent = artwork.description || 'No description available.';
            document.getElementById('modalArtist').textContent = artwork.art_for || 'myself';

            const servicesListEl = document.getElementById('modalServicesList');
            if (artwork.list_service && artwork.list_service.length > 0) {
                servicesListEl.textContent = artwork.list_service.map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(', ');
            } else {
                servicesListEl.textContent = '-';
            }

            const dateStr = artwork.published_at || artwork.created_at;
            if (dateStr) {
                document.getElementById('modalDate').textContent = new Date(dateStr).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } else {
                document.getElementById('modalDate').textContent = 'Unknown';
            }

            document.getElementById('modalOverlay').classList.add('active');
        }

        function openModal(artwork) {
            document.getElementById('modalImage').src = '/storage/' + artwork.image;
            document.getElementById('modalTitle').textContent = artwork.title || 'No Title';

            document.getElementById('modalType').textContent = (artwork.type || '').charAt(0).toUpperCase() + (artwork.type || '').slice(1);
            document.getElementById('modalType').className = 'modal-type tag-' + (artwork.type || '');

            const servicesContainer = document.getElementById('modalServices');
            if (artwork.list_service && artwork.list_service.length > 0) {
                const servicesHtml = artwork.list_service.map(service => {
                    const formattedService = service.charAt(0).toUpperCase() + service.slice(1);
                    return '<span class="modal-service-badge tag-' + service.toLowerCase() + '">' + formattedService + '</span>';
                }).join('');
                servicesContainer.innerHTML = servicesHtml;
                servicesContainer.style.display = 'flex';
            } else {
                servicesContainer.innerHTML = '';
                servicesContainer.style.display = 'none';
            }

            document.getElementById('modalDescription').textContent = artwork.description || 'No description available.';
            document.getElementById('modalArtist').textContent = artwork.art_for || 'myself';

            const servicesListEl = document.getElementById('modalServicesList');
            if (artwork.list_service && artwork.list_service.length > 0) {
                servicesListEl.textContent = artwork.list_service.map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(', ');
            } else {
                servicesListEl.textContent = '-';
            }

            const dateStr = artwork.published_at || artwork.created_at;
            if (dateStr) {
                document.getElementById('modalDate').textContent = new Date(dateStr).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } else {
                document.getElementById('modalDate').textContent = 'Unknown';
            }

            document.getElementById('modalOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(event) {
            if (event.target === event.currentTarget) {
                closeModalDirect();
            }
        }

        function closeModalDirect() {
            document.getElementById('modalOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        function closeServiceModal(event) {
            if (event.target === event.currentTarget) {
                closeServiceModalDirect();
            }
        }

        function closeServiceModalDirect() {
            document.getElementById('serviceModalOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalDirect();
                closeServiceModalDirect();
            }
        });

        // Face API loaded flag
        let faceAPIReady = false;

        // Load Face API models
        async function loadFaceAPI() {
            if (faceAPIReady) return true;

            try {
                // Load from jsDelivr CDN - use tinyFaceDetector for speed
                await faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/model');
                faceAPIReady = true;
                console.log('Face API loaded successfully');
                return true;
            } catch (error) {
                console.error('Failed to load Face API:', error);
                // Fallback to basic face detection
                return false;
            }
        }

        // Smart Crop with Face Detection using face-api.js
        async function smartCropImage(img, container) {
            if (!img.src || img.src.includes('unsplash.com') || img.src.includes('placeholder')) {
                img.style.objectPosition = 'center 30%';
                return;
            }

            // Wait for Face API to be ready
            const apiReady = await loadFaceAPI();
            if (!apiReady) {
                img.style.objectPosition = 'center 30%';
                return;
            }

            try {
                // Create temp image for detection
                const tempImg = document.createElement('img');
                tempImg.crossOrigin = 'anonymous';
                tempImg.src = img.dataset.fullUrl || img.src;

                await new Promise((resolve, reject) => {
                    tempImg.onload = resolve;
                    tempImg.onerror = reject;
                });

                // Detect face using TinyFaceDetector
                const detections = await faceapi.detectSingleFace(tempImg, new faceapi.TinyFaceDetectorOptions({
                    inputSize: 416,
                    scoreThreshold: 0.5
                }));

                if (detections) {
                    // Get face bounding box
                    const box = detections.box;
                    const faceCenterX = box.x + box.width / 2;
                    const faceCenterY = box.y + box.height / 2;

                    // Calculate percentage position
                    let cropX = (faceCenterX / tempImg.width) * 100;
                    let cropY = (faceCenterY / tempImg.height) * 100;

                    // Clamp values to keep face visible but allow some context
                    cropX = Math.max(20, Math.min(cropX, 80));
                    cropY = Math.max(20, Math.min(cropY, 80));

                    img.style.objectFit = 'none';
                    img.style.objectPosition = `${cropX.toFixed(0)}% ${cropY.toFixed(0)}%`;
                    console.log('Face detected! Cropped to:', cropX.toFixed(0) + '%', cropY.toFixed(0) + '%');
                } else {
                    // No face detected, use upper center as fallback
                    img.style.objectPosition = 'center 30%';
                    console.log('No face detected, using fallback');
                }
            } catch (error) {
                console.error('Smart crop error:', error);
                img.style.objectPosition = 'center 30%';
            }
        }

        // Initialize smart crop on page load
        async function initSmartCrop() {
            const smartCropImages = document.querySelectorAll('.smart-crop');

            // Preload Face API
            await loadFaceAPI();

            smartCropImages.forEach(img => {
                const container = img.closest('.card-image') || img.parentElement;
                if (img.complete && img.naturalWidth > 0) {
                    smartCropImage(img, container);
                } else {
                    img.onload = () => smartCropImage(img, container);
                }
            });
        }

        // Run when DOM is ready
        document.addEventListener('DOMContentLoaded', initSmartCrop);

        // TOS Modal Functions
        function closeTosModal() {
            document.getElementById('tosModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Open TOS modal when clicking TOS filter
        document.querySelector('.filter-item[href="#tosModal"]')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('tosModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        document.getElementById('openProgressBoard')?.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('progressBoardModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        // Close TOS modal when clicking overlay
        document.getElementById('tosModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeTosModal();
            }
        });

        document.getElementById('progressBoardModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeProgressBoard();
            }
        });

        function closeProgressBoard() {
            document.getElementById('progressBoardModal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close TOS modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTosModal();
                closeProgressBoard();
            }
        });
    </script>
    <script src="{{ asset('js/mobile-fullscreen-nav.js') }}"></script>

    <style>
        /* Filter Menu Styles (like artwork filter) */
        .coms_menu {
            padding: 20px 0;
            position: sticky;
            top: 85px;
            z-index: 100;
            background-color: white;
        }

        .coms_menu_container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .filter-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .filter-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 30px;
            background-color: white;
            border: var(--black, #1f1b18) 2px solid;
            border-radius: 50px;
            color: var(--black, #1f1b18);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .filter-item:hover {
            background-color: #efe6db;
        }

        .filter-item.active {
            background-color: var(--black, #1f1b18);
            color: white;
        }

        .progress-board-modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0,0,0,0.65);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2100;
            padding: 20px;
        }

        .progress-board-modal-overlay.active {
            display: flex;
        }

        .progress-board-modal-content {
            width: min(1200px, 100%);
            max-height: 90vh;
            overflow-y: auto;
            background: white;
            border-radius: 28px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.18);
            padding: 28px;
        }

        .progress-board-header {
            max-width: 1200px;
            margin: 40px auto 0;
            padding: 0 20px 40px;
        }

        .progress-board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .progress-board-header h2 {
            margin: 0;
            font-size: 32px;
            color: var(--black, #1f1b18);
        }

        .board-note {
            margin: 0;
            color: #5a3f48;
            max-width: 760px;
            line-height: 1.6;
        }

        .kanban-container {
            display: grid;
            grid-template-columns: repeat(5, minmax(180px, 1fr));
            gap: 18px;
        }

        .kanban-column {
            background: #f7f2ee;
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100px;
        }

        .kanban-column-header {
            padding: 18px 16px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .kanban-column-header h3 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .kanban-column-header span {
            background: rgba(255,255,255,0.25);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 13px;
            color: white;
        }

        .kanban-column-body {
            padding: 16px;
            display: grid;
            gap: 14px;
        }

        .client-card {
            background: white;
            border-radius: 18px;
            border: 1px solid #e5d7c4;
            padding: 16px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.04);
            color: #312e2a;
        }

        .client-card.readonly {
            cursor: default;
        }

        .client-card-title {
            margin: 0 0 6px;
            font-weight: 700;
            font-size: 16px;
        }

        .client-card-subtitle {
            margin: 0 0 12px;
            color: #7a6c64;
            font-size: 13px;
        }

        .client-card-details {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            margin-bottom: 12px;
            font-size: 13px;
            color: #5f4f47;
        }

        .client-card-details span {
            background: #f5f0ea;
            border-radius: 999px;
            padding: 6px 10px;
        }

        .client-card-items {
            display: grid;
            gap: 8px;
        }

        .client-card-item {
            background: #fff7ef;
            color: #7a5b41;
            border-radius: 14px;
            padding: 8px 12px;
            font-size: 13px;
        }

        .empty-column {
            color: #8c7f74;
            font-size: 14px;
            text-align: center;
            padding: 24px 8px;
            background: rgba(255,255,255,0.85);
            border-radius: 16px;
        }

        @media (max-width: 1024px) {
            .kanban-container {
                grid-template-columns: repeat(2, minmax(180px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .kanban-container {
                grid-template-columns: 1fr;
            }
        }

        /* TOS Modal Styles */
        .tos-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
            padding: 20px;
        }

        .tos-modal-overlay.active {
            display: flex;
        }

        .tos-modal-content {
            background-color: white;
            border-radius: 20px;
            max-width: 800px;
            width: 100%;
            max-height: 80vh;
            overflow-y: auto;
            padding: 30px 40px;
        }

        .tos-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            position: sticky;
            top: 0;
            background: white;
            padding-bottom: 16px;
            border-bottom: 1px solid #eee;
        }

        .tos-modal-header h2 {
            margin: 0;
            color: var(--black, #1f1b18);
            font-size: 24px;
        }

        .tos-modal-close {
            background: none;
            border: none;
            font-size: 32px;
            color: var(--black, #1f1b18);
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .tos-modal-close:hover {
            color: #ff9543;
        }

        .tos-modal-body {
            line-height: 1.8;
            color: #5a3f48;
        }

        .tos-modal-body h3 {
            color: var(--black, #1f1b18);
            margin-top: 24px;
            margin-bottom: 12px;
        }

        .tos-modal-body p, .tos-modal-body li {
            margin-bottom: 12px;
        }

        .tos-modal-body ul {
            padding-left: 24px;
        }

        @media (max-width: 768px) {
            .filter-item {
                padding: 10px 18px;
                font-size: 14px;
            }

            .tos-modal-content {
                padding: 20px 24px;
            }

            .tos-modal-header h2 {
                font-size: 20px;
            }

            .tos-modal-body {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .filter-item {
                padding: 8px 14px;
                font-size: 13px;
            }

            .tos-modal-content {
                padding: 16px 20px;
                border-radius: 16px;
            }

            .tos-modal-header h2 {
                font-size: 18px;
            }

            .tos-modal-close {
                font-size: 24px;
            }
        }

        /* Service badge styles for modal */
        .detailcoms{
            width: 50%;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }
        .latestcoms{
            width: 50%;
            aspect-ratio: 4/5;
            background-color: var(--black);
            border-radius: 16px;
            overflow: hidden;
        }
        .latestcoms img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fiturcoms{
            padding-bottom: 20px;
            flex-shrink: 0;
        }
        .pricecoms{
            padding-bottom: 16px;
            flex-shrink: 0;
        }
        .pricecoms .card-price {
            font-size: 14px;
            color: #8c7f74;
            margin: 0;
        }
        .pricecoms .card-price .price {
            font-weight: 700;
            color: #ff9543;
            font-size: 20px;
        }
        .modal-service-badge {
            display: inline-flex;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            color: white;
            text-transform: capitalize;
        }

        .tag-headshot { background-color: #e8a87c; }
        .tag-halfbody { background-color: #c38c9c; }
        .tag-fullbody { background-color: #85c1ae; }
        .tag-chibi { background-color: #9bc1e8; }

        /* Service Gallery Modal */
        .service-modal-content {
            max-width: 80vw;
            max-height: 80vh;
            padding: 30px;
            gap: 30px;
            overflow: hidden;
            width: 900px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            width: 100%;
            flex-shrink: 0;
        }

        .modal-header h2 {
            font-size: 24px;
            color: #5a3f48;
            margin: 0;
        }

        .modal-header .modal-close-btn {
            background: none;
            border: none;
            font-size: 30px;
            color: #5a3f48;
            cursor: pointer;
            padding: 0;
            margin: 0;
        }

        .modal-header .modal-close-btn:hover {
            background: none;
            color: #ff9543;
        }

        .service-gallery-grid {
            display: flex;
            gap: 16px;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 10px;
            flex: 1;
            min-height: 0;
            align-items: stretch;
        }

        .service-gallery-grid::-webkit-scrollbar {
            height: 8px;
        }

        .service-gallery-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .service-gallery-grid::-webkit-scrollbar-thumb {
            background: #c38c9c;
            border-radius: 10px;
        }

        .service-artwork-item {
            position: relative;
            aspect-ratio: 3/4;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            flex-shrink: 0;
            width: 180px;
            height: auto;
            min-height: 200px;
        }

        .service-artwork-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .service-artwork-item:hover img {
            transform: scale(1.05);
        }

        .service-artwork-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
            padding: 20px 10px 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .service-artwork-item:hover .service-artwork-overlay {
            opacity: 1;
        }

        .service-artwork-overlay h4 {
            color: white;
            margin: 0;
            font-size: 14px;
        }

        .no-artworks {
            text-align: center;
            color: #888;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .service-modal-content {
                flex-direction: column;
                padding: 20px;
                gap: 20px;
                width: 95vw;
                max-height: 90vh;
            }

            .latestcoms {
                width: 100%;
                aspect-ratio: 4/5;
                max-height: 300px;
            }

            .detailcoms {
                width: 100%;
                overflow-y: auto;
            }

            .modal-header {
                margin-bottom: 12px;
            }

            .modal-header h2 {
                font-size: 20px;
            }

            .pricecoms {
                padding-bottom: 12px;
            }

            .pricecoms .card-price .price {
                font-size: 18px;
            }

            .fiturcoms {
                padding-bottom: 12px;
            }

            .fiturcoms h2 {
                font-size: 16px;
            }

            .fiturcoms ul li {
                font-size: 13px;
            }

            .service-gallery-grid {
                gap: 12px;
                padding-bottom: 8px;
            }

            .service-artwork-item {
                width: 140px;
                min-height: 160px;
            }
        }

        @media (max-width: 480px) {
            .service-modal-content {
                padding: 16px;
                gap: 16px;
                width: 98vw;
                border-radius: 16px;
            }

            .latestcoms {
                aspect-ratio: 4/5;
                max-height: 250px;
            }

            .modal-header h2 {
                font-size: 18px;
            }

            .modal-header .modal-close-btn {
                font-size: 24px;
            }

            .pricecoms {
                padding-bottom: 10px;
            }

            .pricecoms .card-price {
                font-size: 13px;
            }

            .pricecoms .card-price .price {
                font-size: 16px;
            }

            .fiturcoms h2 {
                font-size: 14px;
                margin-bottom: 8px;
            }

            .fiturcoms ul li {
                font-size: 12px;
            }

            .service-gallery-grid {
                gap: 10px;
            }

            .service-artwork-item {
                width: 120px;
                min-height: 140px;
            }

            .service-artwork-overlay h4 {
                font-size: 12px;
            }
        }
    </style>
</body>
</html>
