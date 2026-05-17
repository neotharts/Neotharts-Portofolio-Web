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
            <a href="/">Home</a>
            <a href="/artworks">Artworks</a>
            <a href="/commission" class="active">Commissions</a>
            <a href="/contact">Contact</a>
        </div>
        <div class="mainavmobile">
            <span class="material-icons">menu</span>
        </div>
    </nav>

    <section class="gallery-header">
        <div class="mainheaders">
            <img class="desktop" src="{{ asset('img/COMMISSION.png') }}" alt="Commission">
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
                        <p class="card-price">Mulai dari <span class="price">Rp {{ number_format($service->starting_price, 0, ',', '.') }}</span></p>
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
                    <p class="card-price">Mulai dari <span id="modalServicePrice" class="price">Rp 0</span></p>
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
            const formattedPrice = new Intl.NumberFormat('id-ID').format(price || 0);
            document.getElementById('modalServicePrice').textContent = 'Rp ' + formattedPrice;

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
    </script>

    <style>
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