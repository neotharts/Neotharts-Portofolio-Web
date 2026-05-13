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
            <a href="">Commissions</a>
            <a href="">Contact</a>
        </div>
        <div class="mainavmobile">
            <span class="material-icons">menu</span>
        </div>
    </nav>

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
            <div class="modal-image">
                <img id="modalImage" src="" alt="">
            </div>
            <div class="modal-info">
                <h2 class="modal-title" id="modalTitle"></h2>
                <div class="modal-tags">
                    <span class="modal-type" id="modalType"></span>
                    <span class="modal-form" id="modalForm"></span>
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
                </div>
                <button class="modal-close-btn" onclick="closeModalDirect()">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Store all artworks data
        const allArtworks = {!! json_encode($artworks) !!};
        let currentFilter = '';

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
            return cols * 2; // Show 2 rows worth
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

        // Check if all images are loaded
        function checkAllImagesLoaded() {
            const images = document.querySelectorAll('.gallery-content .placeholder img');
            if (images.length === 0) return true;

            let loadedCount = 0;
            images.forEach(img => {
                if (img.complete && img.naturalHeight !== 0) {
                    loadedCount++;
                } else {
                    img.addEventListener('load', onImageLoad);
                    img.addEventListener('error', onImageLoad);
                }
            });

            return loadedCount === images.length;
        }

        function onImageLoad() {
            loadedCount++;
            if (loadedCount >= totalImages) {
                hideSkeleton();
            }
        }

        // Hide skeleton and show gallery
        function hideSkeleton() {
            const skeleton = document.getElementById('skeletonWrapper');
            const gallery = document.getElementById('galleryContent');

            if (skeleton) skeleton.style.display = 'none';
            if (gallery) gallery.style.opacity = '1';
        }

        function openModal(id) {
            const artwork = allArtworks.find(a => a.id === id);
            if (!artwork) return;

            document.getElementById('modalImage').src = '/storage/' + artwork.image;
            document.getElementById('modalTitle').textContent = artwork.title;
            document.getElementById('modalType').textContent = artwork.type.charAt(0).toUpperCase() + artwork.type.slice(1);
            document.getElementById('modalType').className = 'modal-type tag-' + artwork.type;
            document.getElementById('modalForm').textContent = artwork.form.charAt(0).toUpperCase() + artwork.form.slice(1);
            document.getElementById('modalForm').className = 'modal-form tag-' + artwork.form;
            document.getElementById('modalDescription').textContent = artwork.description || 'No description available.';
            document.getElementById('modalArtist').textContent = artwork.art_for || 'myself';
            document.getElementById('modalDate').textContent = new Date(artwork.published_at || artwork.created_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

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

            // Re-init lazy loading for new images
            initLazyLoading();
        }

        // Filter click handler
        document.querySelectorAll('.filter-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                const filter = this.dataset.filter;

                // Toggle: if already selected, deselect (show all)
                if (this.classList.contains('active')) {
                    currentFilter = '';
                    document.querySelectorAll('.filter-item').forEach(f => f.classList.remove('active'));
                    renderGallery(allArtworks);
                } else {
                    // Apply filter
                    currentFilter = filter;
                    document.querySelectorAll('.filter-item').forEach(f => f.classList.remove('active'));
                    this.classList.add('active');

                    const filtered = allArtworks.filter(a => a.type === filter);
                    renderGallery(filtered);
                }
            });
        });

        // Initialize gallery
        renderGallery(allArtworks);
        initLazyLoading();

        // Count images and wait for all to load
        totalImages = document.querySelectorAll('.gallery-content .placeholder img').length;
        loadedCount = 0;

        if (totalImages === 0) {
            hideSkeleton();
        } else {
            // Track loading for each image
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

            // Fallback: hide skeleton after 5 seconds max
            setTimeout(() => {
                hideSkeleton();
            }, 5000);
        }
    </script>

    </body>
</html>