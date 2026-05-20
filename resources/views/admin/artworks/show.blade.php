@extends('admin.layout')

@section('pageTitle', 'Detail Artwork')

@section('content')
    <div class="detail-card glass-card">
        <div class="detail-header">
            <div>
                <p class="eyebrow">Detail Artwork</p>
                <h2>{{ $artwork->title }}</h2>
            </div>
            <div class="detail-actions">
                <a href="{{ route('admin.artworks.edit', $artwork) }}" class="button button-soft">Edit</a>
                <form action="{{ route('admin.artworks.destroy', $artwork) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="button button-danger">Hapus</button>
                </form>
                <a href="{{ route('admin.artworks.index') }}" class="button button-outline">Kembali</a>
            </div>
        </div>

        <div class="detail-content detail-carousel-layout">
            <div class="detail-image-section artwork-carousel">
                <div class="carousel-stage">
                    @if(count($galleryImages) > 1)
                        <button type="button" class="carousel-nav carousel-nav-prev" aria-label="Gambar sebelumnya" data-carousel-prev>
                            <span class="material-icons-outlined">chevron_left</span>
                        </button>
                    @endif

                    @if(count($galleryImages) > 0)
                        @foreach($galleryImages as $index => $image)
                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $artwork->title }} {{ $index + 1 }}" class="detail-image carousel-image {{ $index === 0 ? 'active' : '' }}" data-carousel-image="{{ $index }}">
                        @endforeach
                    @else
                        <div class="carousel-empty">
                            <span class="material-icons-outlined">image_not_supported</span>
                            <p>No image</p>
                        </div>
                    @endif

                    @if(count($galleryImages) > 1)
                        <button type="button" class="carousel-nav carousel-nav-next" aria-label="Gambar berikutnya" data-carousel-next>
                            <span class="material-icons-outlined">chevron_right</span>
                        </button>
                    @endif

                    @if(count($galleryImages) > 0)
                        <div class="carousel-counter">
                            <span data-carousel-current>1</span> / {{ count($galleryImages) }}
                        </div>
                    @endif
                </div>

                @if(count($galleryImages) > 1)
                    <div class="carousel-thumbnails" aria-label="Pilih gambar artwork">
                        @foreach($galleryImages as $index => $image)
                            <button type="button" class="carousel-thumb {{ $index === 0 ? 'active' : '' }}" data-carousel-thumb="{{ $index }}" title="Gambar {{ $index + 1 }}">
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $artwork->title }} {{ $index + 1 }}">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="detail-info-section">
                <div class="info-group">
                    <h3>Deskripsi</h3>
                    <p>{{ $artwork->description }}</p>
                </div>

                <div class="info-group">
                    <h3>Kategori</h3>
                    <div class="info-badges">
                        <span class="badge tag-{{ $artwork->type }}">Tipe: {{ ucfirst($artwork->type) }}</span>
                        @if($artwork->form)
                            <span class="badge badge-soft">Form: {{ ucfirst($artwork->form) }}</span>
                        @endif
                    </div>
                </div>

                @if($artwork->list_service && count($artwork->list_service) > 0)
                    <div class="info-group">
                        <h3>List Service</h3>
                        <div class="service-badges">
                            @foreach($artwork->list_service as $service)
                                <span class="service-badge tag-{{ \Illuminate\Support\Str::slug($service) }}">{{ ucfirst(str_replace('-', ' ', $service)) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="info-group">
                    <h3>Status</h3>
                    @if($artwork->is_published)
                        <span class="badge badge-success">Dipublikasikan</span>
                        @if($artwork->published_at)
                            <p class="muted-text">Tanggal publikasi: {{ $artwork->published_at->format('d F Y H:i') }}</p>
                        @endif
                    @else
                        <span class="badge badge-warning">Draft</span>
                    @endif
                </div>

                <div class="info-group">
                    <h3>Jumlah Gambar</h3>
                    <p>{{ count($galleryImages) }} gambar</p>
                </div>

                <div class="info-group">
                    <h3>Urutan Tampil</h3>
                    <p>{{ $artwork->sort_order }}</p>
                </div>

                <div class="info-group">
                    <h3>Art For</h3>
                    <p>{{ $artwork->art_for ?? 'myself' }}</p>
                </div>

                <div class="info-group">
                    <h3>Informasi Upload</h3>
                    <p>Dibuat: {{ $artwork->created_at->format('d F Y H:i') }}</p>
                    @if($artwork->created_at !== $artwork->updated_at)
                        <p>Diperbarui: {{ $artwork->updated_at->format('d F Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .detail-carousel-layout {
            grid-template-columns: minmax(0, 1.25fr) minmax(320px, 0.75fr);
            align-items: start;
        }

        .artwork-carousel {
            display: grid;
            gap: 16px;
        }

        .service-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .service-badge {
            display: inline-flex;
            padding: 6px 14px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 500;
        }

        .tag-headshot { background: #e8a87c; color: white; }
        .tag-halfbody { background: #c38c9c; color: white; }
        .tag-fullbody { background: #85c1ae; color: white; }
        .tag-chibi { background: #9bc1e8; color: white; }

        .badge-soft {
            background: rgba(255, 149, 67, 0.12);
            color: var(--accent-strong);
        }

        .carousel-stage {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            background: #f7efe6;
            min-height: 460px;
            display: grid;
            place-items: center;
        }

        .carousel-image {
            min-height: 460px;
            max-height: 640px;
            object-fit: contain;
            background: #f7efe6;
            display: none;
        }

        .carousel-image.active {
            display: block;
        }

        .carousel-empty {
            min-height: 460px;
            display: grid;
            place-items: center;
            align-content: center;
            gap: 8px;
            color: var(--muted);
        }

        .carousel-empty .material-icons-outlined {
            font-size: 48px;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: var(--text);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(31, 27, 24, 0.08);
            text-decoration: none;
            box-shadow: 0 12px 28px rgba(31, 27, 24, 0.12);
            transition: transform 0.2s ease, background 0.2s ease;
            z-index: 2;
            cursor: pointer;
        }

        .carousel-nav:hover {
            transform: translateY(-50%) scale(1.04);
            background: #fff;
            color: var(--accent-strong);
        }

        .carousel-nav-prev {
            left: 16px;
        }

        .carousel-nav-next {
            right: 16px;
        }

        .carousel-counter {
            position: absolute;
            right: 16px;
            bottom: 16px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(31, 27, 24, 0.72);
            color: white;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .carousel-thumbnails {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 76px;
            gap: 10px;
            overflow-x: auto;
            padding: 2px 2px 10px;
            scrollbar-width: thin;
        }

        .carousel-thumb {
            width: 76px;
            aspect-ratio: 1;
            border-radius: 16px;
            overflow: hidden;
            display: grid;
            place-items: center;
            background: #fff7ef;
            color: var(--muted);
            border: 2px solid transparent;
            box-shadow: 0 10px 24px rgba(31, 27, 24, 0.08);
            transition: border-color 0.2s ease, transform 0.2s ease;
            cursor: pointer;
            padding: 0;
        }

        .carousel-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .carousel-thumb.active,
        .carousel-thumb:hover {
            border-color: var(--accent-strong);
            transform: translateY(-2px);
        }

        @media (max-width: 980px) {
            .detail-carousel-layout {
                grid-template-columns: 1fr;
            }

            .carousel-stage,
            .carousel-image,
            .carousel-empty {
                min-height: 320px;
            }
        }

        @media (max-width: 560px) {
            .carousel-stage,
            .carousel-image,
            .carousel-empty {
                min-height: 260px;
            }

            .carousel-nav {
                width: 38px;
                height: 38px;
            }

            .carousel-thumbnails {
                grid-auto-columns: 64px;
            }

            .carousel-thumb {
                width: 64px;
                border-radius: 14px;
            }
        }
    </style>

    <script>
        const carouselImages = Array.from(document.querySelectorAll('[data-carousel-image]'));
        const carouselThumbs = Array.from(document.querySelectorAll('[data-carousel-thumb]'));
        const carouselCurrent = document.querySelector('[data-carousel-current]');
        let activeCarouselIndex = 0;

        function showCarouselImage(index) {
            if (carouselImages.length === 0) {
                return;
            }

            activeCarouselIndex = (index + carouselImages.length) % carouselImages.length;

            carouselImages.forEach((image, imageIndex) => {
                image.classList.toggle('active', imageIndex === activeCarouselIndex);
            });

            carouselThumbs.forEach((thumb, thumbIndex) => {
                thumb.classList.toggle('active', thumbIndex === activeCarouselIndex);
            });

            if (carouselCurrent) {
                carouselCurrent.textContent = activeCarouselIndex + 1;
            }
        }

        document.querySelector('[data-carousel-prev]')?.addEventListener('click', () => {
            showCarouselImage(activeCarouselIndex - 1);
        });

        document.querySelector('[data-carousel-next]')?.addEventListener('click', () => {
            showCarouselImage(activeCarouselIndex + 1);
        });

        carouselThumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => showCarouselImage(index));
        });
    </script>
@endsection
