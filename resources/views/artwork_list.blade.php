<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Artwork Gallery</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite('resources/css/home.css')
    @vite('resources/css/artwork_list.css')
</head>
<body>
    <nav>
        <div class="mainav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('artworks') }}">Artworks</a>
            <a href="{{ route('commission') }}">Commissions</a>
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
            <img class="desktop" src="{{ asset('img/artwork_title.png') }}" alt="Artwork Gallery">
        </div>
</section>

    <section id="gallery">
        <div class="gallery-container">
            <!-- Sidebar Filter -->
            <div class="sidebar-filter">
                <div class="filter-list">
                    @foreach($types as $type)
                        <a href="#" class="filter-item {{ request('type') === $type ? 'active' : '' }}" data-filter="{{ $type }}">
                            <span>{{ ucfirst($type) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Skeleton Loading -->
            <div class="skeleton-wrapper" id="skeletonWrapper">
                <div class="skeleton-grid" id="skeletonGrid">
                    <!-- Skeleton items will be added by JS -->
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-content" id="galleryContent" style="opacity: 0;">
                @if($artworks->count() > 0)
                    <div class="placeholders">
                        @foreach($artworks as $artwork)
                            <div class="placeholder" onclick="openModal({{ $artwork->id }})">
                                @if($artwork->image)
                                    <img src="{{ asset('storage/' . $artwork->image) }}" alt="{{ $artwork->title }}" class="lazy-img" loading="lazy">
                                @else
                                    <div class="placeholder-empty">No Image</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <span>No artworks found</span>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal-overlay" id="modalOverlay" onclick="closeModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-image modal-carousel">
                <button type="button" class="modal-carousel-btn modal-carousel-prev" id="modalPrevBtn" onclick="changeModalImage(-1)" aria-label="Previous image">
                    <span class="material-icons">chevron_left</span>
                </button>
                <img id="modalImage" src="" alt="">
                <button type="button" class="modal-carousel-btn modal-carousel-next" id="modalNextBtn" onclick="changeModalImage(1)" aria-label="Next image">
                    <span class="material-icons">chevron_right</span>
                </button>
                <div class="modal-carousel-counter" id="modalImageCounter"></div>
                <div class="modal-carousel-thumbs" id="modalImageThumbs"></div>
            </div>
            <div class="modal-info">
                <h2 class="modal-title" id="modalTitle"></h2>
                <div class="modal-tags">
                    <span class="modal-type" id="modalType"></span>
                    <span class="modal-services" id="modalServices"></span>
                </div>
                <p class="modal-description" id="modalDescription"></p>
                <div class="modal-meta">
                    <div class="modal-meta-item">
                        <span>Art For:</span>
                        <span id="modalArtist"></span>
                    </div>
                    <div class="modal-meta-item">
                        <span>Date:</span>
                        <span id="modalDate"></span>
                    </div>
                    <div class="modal-meta-item">
                        <span>Services:</span>
                        <span id="modalServicesList"></span>
                    </div>
                </div>
                <button class="modal-close-btn" onclick="closeModalDirect()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Store all artworks data
        const allArtworks = {!! json_encode($artworksArray ?? $artworks->toArray()) !!};

        // Get initial filter from URL
        const urlParams = new URLSearchParams(window.location.search);
        let currentTypeFilter = urlParams.get('type') || '';
        let currentServiceFilter = urlParams.get('service') || '';
        let currentModalImages = [];
        let currentModalImageIndex = 0;
        let modalSlideDirection = 'next';

        console.log('All artworks loaded:', allArtworks.length, allArtworks);

        // Generate skeleton items to fill screen
        function generateSkeleton(count) {
            const grid = document.getElementById('skeletonGrid');
            let html = '';
            for (let i = 0; i < count; i++) {
                html += '<div class="skeleton-item"></div>';
            }
            grid.innerHTML = html;
        }

        // Calculate how many skeleton items to show
        function getSkeletonCount() {
            const width = window.innerWidth;
            let cols = 5;
            if (width <= 480) cols = 2;
            else if (width <= 768) cols = 3;
            else if (width <= 1024) cols = 4;
            return cols * 2;
        }

        // Initialize skeleton
        generateSkeleton(getSkeletonCount());

        // Initialize lazy loading for images
        function initLazyLoading() {
            const images = document.querySelectorAll('.lazy-img');
            images.forEach(img => {
                img.onload = function() {
                    img.classList.add('loaded');
                };
                img.onerror = function() {
                    img.classList.add('loaded');
                };
            });
        }

        // Hide skeleton and show gallery
        function hideSkeleton() {
            const skeleton = document.getElementById('skeletonWrapper');
            const gallery = document.getElementById('galleryContent');

            if (skeleton) skeleton.style.display = 'none';
            if (gallery) gallery.style.opacity = '1';
        }

        function openModal(id) {
            const artwork = allArtworks.find(a => a.id == id);
            if (!artwork) {
                console.log('Artwork not found, id:', id, 'allArtworks:', allArtworks);
                return;
            }

            setupModalImages(artwork);
            document.getElementById('modalTitle').textContent = artwork.title || 'No Title';

            // Type badge
            document.getElementById('modalType').textContent = (artwork.type || '').charAt(0).toUpperCase() + (artwork.type || '').slice(1);
            document.getElementById('modalType').className = 'modal-type tag-' + (artwork.type || '');

            // Services badges
            const servicesContainer = document.getElementById('modalServices');
            if (artwork.list_service && artwork.list_service.length > 0) {
                const servicesHtml = artwork.list_service.map(service => {
                    const formattedService = service.charAt(0).toUpperCase() + service.slice(1);
                    return '<span class="modal-service-badge tag-' + service + '">' + formattedService + '</span>';
                }).join('');
                servicesContainer.innerHTML = servicesHtml;
                servicesContainer.style.display = 'flex';
            } else {
                servicesContainer.innerHTML = '';
                servicesContainer.style.display = 'none';
            }

            document.getElementById('modalDescription').textContent = artwork.description || 'No description available.';
            document.getElementById('modalArtist').textContent = artwork.art_for || 'myself';

            // Services list in meta
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

        function setupModalImages(artwork) {
            currentModalImages = Array.isArray(artwork.images) && artwork.images.length > 0
                ? artwork.images
                : (artwork.image ? [artwork.image] : []);
            currentModalImageIndex = 0;
            modalSlideDirection = 'next';

            const prevBtn = document.getElementById('modalPrevBtn');
            const nextBtn = document.getElementById('modalNextBtn');
            const counter = document.getElementById('modalImageCounter');
            const thumbs = document.getElementById('modalImageThumbs');
            const hasMultipleImages = currentModalImages.length > 1;

            prevBtn.style.display = hasMultipleImages ? 'grid' : 'none';
            nextBtn.style.display = hasMultipleImages ? 'grid' : 'none';
            counter.style.display = currentModalImages.length > 0 ? 'block' : 'none';
            thumbs.style.display = hasMultipleImages ? 'flex' : 'none';

            thumbs.innerHTML = currentModalImages.map((image, index) => `
                <button type="button" class="modal-carousel-thumb ${index === 0 ? 'active' : ''}" onclick="setModalImage(${index})" aria-label="Show image ${index + 1}">
                    <img src="/storage/${image}" alt="${artwork.title || 'Artwork'} ${index + 1}">
                </button>
            `).join('');

            renderModalImage();
        }

        function renderModalImage() {
            const modalImage = document.getElementById('modalImage');
            const counter = document.getElementById('modalImageCounter');
            const thumbs = document.querySelectorAll('.modal-carousel-thumb');

            if (currentModalImages.length === 0) {
                modalImage.removeAttribute('src');
                modalImage.alt = 'No image';
                counter.textContent = '';
                return;
            }

            modalImage.src = '/storage/' + currentModalImages[currentModalImageIndex];
            modalImage.classList.remove('slide-next', 'slide-prev');
            void modalImage.offsetWidth;
            modalImage.classList.add(modalSlideDirection === 'prev' ? 'slide-prev' : 'slide-next');
            counter.textContent = `${currentModalImageIndex + 1} / ${currentModalImages.length}`;

            thumbs.forEach((thumb, index) => {
                thumb.classList.toggle('active', index === currentModalImageIndex);
            });
        }

        function setModalImage(index, direction = 'next') {
            if (currentModalImages.length === 0) return;

            modalSlideDirection = direction;
            currentModalImageIndex = (index + currentModalImages.length) % currentModalImages.length;
            renderModalImage();
        }

        function changeModalImage(direction) {
            setModalImage(currentModalImageIndex + direction, direction < 0 ? 'prev' : 'next');
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

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModalDirect();
            }
        });

        // Render gallery
        function renderGallery(artworks) {
            const placeholders = document.querySelector('.gallery-content .placeholders');
            if (!placeholders) return;

            if (artworks.length === 0) {
                placeholders.innerHTML = '<div class="empty-state"><span>No artworks found</span></div>';
                return;
            }

            placeholders.innerHTML = artworks.map(artwork => `
                <div class="placeholder" onclick="openModal(${artwork.id})">
                    ${artwork.image
                        ? `<img src="/storage/${artwork.image}" alt="${artwork.title}" class="lazy-img" loading="lazy">`
                        : '<div class="placeholder-empty">No Image</div>'
                    }
                </div>
            `).join('');

            initLazyLoading();
        }

        // Apply filters
        function applyFilters() {
            let filtered = allArtworks;

            // Filter by type
            if (currentTypeFilter) {
                filtered = filtered.filter(a => a.type === currentTypeFilter);
            }

            // Filter by service
            if (currentServiceFilter) {
                filtered = filtered.filter(a => {
                    if (!a.list_service || !Array.isArray(a.list_service)) return false;
                    return a.list_service.includes(currentServiceFilter);
                });
            }

            renderGallery(filtered);
        }

        // Initialize active states from URL
        function initFilterStates() {
            // No active state on load - filter starts empty (shows all)
            // Active state will be set when user clicks a filter
        }

        // Type filter click handler
        document.querySelectorAll('.filter-item[data-filter]').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                const filter = this.dataset.filter;

                // Toggle: if already selected, deselect (show all)
                if (this.classList.contains('active')) {
                    currentTypeFilter = '';
                    this.classList.remove('active');
                } else {
                    document.querySelectorAll('.filter-item[data-filter]').forEach(f => f.classList.remove('active'));
                    this.classList.add('active');
                    currentTypeFilter = filter;
                }

                applyFilters();
            });
        });

        // Service filter click handler
        document.querySelectorAll('.filter-service').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                const service = this.dataset.service;

                // Toggle: if already selected, deselect (show all)
                if (this.classList.contains('active')) {
                    currentServiceFilter = '';
                    this.classList.remove('active');
                } else {
                    document.querySelectorAll('.filter-service').forEach(f => f.classList.remove('active'));
                    this.classList.add('active');
                    currentServiceFilter = service;
                }

                applyFilters();
            });
        });

        // Initialize gallery
        initFilterStates();
        applyFilters();
        initLazyLoading();

        // Count images and wait for all to load
        let totalImages = document.querySelectorAll('.gallery-content .placeholder img').length;
        let loadedCount = 0;

        if (totalImages === 0) {
            hideSkeleton();
        } else {
            document.querySelectorAll('.gallery-content .placeholder img').forEach(img => {
                if (img.complete && img.naturalHeight !== 0) {
                    loadedCount++;
                } else {
                    img.addEventListener('load', function() {
                        loadedCount++;
                        if (loadedCount >= totalImages) {
                            hideSkeleton();
                        }
                    });
                    img.addEventListener('error', function() {
                        loadedCount++;
                        if (loadedCount >= totalImages) {
                            hideSkeleton();
                        }
                    });
                }
            });

            setTimeout(() => {
                hideSkeleton();
            }, 5000);
        }
    </script>
    <script src="{{ asset('js/mobile-fullscreen-nav.js') }}"></script>

    <style>
        /* Service badge styles */
        .modal-service-badge {
            display: inline-flex;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            align-items: center;
            margin-right: 6px;
            color: white;
        }

        .modal-services {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .tag-headshot { background: #e8a87c; }
        .tag-halfbody { background: #c38c9c; }
        .tag-fullbody { background: #85c1ae; }
        .tag-chibi { background: #9bc1e8; }

        .filter-divider {
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 16px 0;
        }

        .filter-services {
            opacity: 0.8;
        }

        .filter-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            margin-bottom: 8px;
            padding-left: 12px;
        }

        .filter-service.active {
            background: rgba(232, 168, 124, 0.2);
        }
    </style>

    </body>
</html>
