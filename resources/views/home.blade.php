<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite('resources/css/home.css')
    <link rel="stylesheet" href="{{ asset('css/live2d.css') }}">
    <style>
        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loading-screen.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loading-content {
            text-align: center;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f7f1ea;
            border-top: 4px solid var(--orange);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .loading-text {
            font-size: 18px;
            color: var(--black);
            font-weight: 500;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p class="loading-text">Loading...</p>
        </div>
    </div>

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

    <header>
        <div class="img">
            <img src="{{ asset('img/hello.png') }}" alt="Hello">
        </div>
        <div class="mainheader" data-scroll-reveal>
            <img class="desktop" src="{{ asset('img/header_pc_web.png') }}" alt="Main Header">
            <img class="mobile" src="{{ asset('img/header_mobile_web.png') }}" alt="Main Header">
            <div class="bottommore">
                <a href="#latest"> See more </a>
            </div>
        </div>
    </header>
    <section id="latest">
        <div class="text" data-scroll-reveal>
            <img class="desktop" src="{{ asset('img/latest_text.png') }}" alt="Latest Artworks">
            <img class="mobile" src="{{ asset('img/latest_text_mobile.png') }}" alt="Latest Artwork">
        </div>
        <div class="lo" data-scroll-reveal>
        <div class="placeholders">
            @if($artworks->count() > 0)
                @foreach($artworks as $artwork)
                    <a href="{{ route('artworks', ['artwork' => $artwork->id]) }}" class="placeholder">
                        @if($artwork->image)
                            <img src="{{ asset('storage/' . $artwork->image) }}" alt="{{ $artwork->title }}">
                        @else
                            <div class="placeholder-empty">No Image</div>
                        @endif
                    </a>
                @endforeach

                @for($i = $artworks->count(); $i < 3; $i++)
                    <a href="/artworks" class="placeholder placeholder-empty">
                        <span>Artwork Coming Soon</span>
                    </a>
                @endfor
            @else
                @for($i = 0; $i < 3; $i++)
                    <a href="/artworks" class="placeholder placeholder-empty">
                        <span>Artwork Coming Soon</span>
                    </a>
                @endfor
            @endif

            <div class="kotak"></div>
        </div>
        </div>

        <div class="bottommore" data-scroll-reveal>
                <a href="/artworks"> See more </a>
        </div>

        <div class="latest-live2d-showcase" data-scroll-reveal>
            <div class="latest-hello">
                <img src="{{ asset('img/HELLO01.png') }}" alt="Hello">
            </div>

            <div
                id="live2d-widget"
                class="live2d-widget"
                data-model-src="{{ asset('kai/Kai model.model3.json') }}"
                data-vtube-src="{{ asset('kai/Kai model.vtube.json') }}"
                aria-hidden="true"
            >
                <canvas id="live2d-canvas" class="live2d-canvas"></canvas>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/pixi.js@6.5.10/dist/browser/pixi.min.js"></script>
    <script src="https://cubism.live2d.com/sdk-web/cubismcore/live2dcubismcore.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pixi-live2d-display@0.4.0/dist/cubism4.min.js"></script>
    <script src="{{ asset('js/live2d-home.js') }}"></script>

    <script>
        // Hide loading screen when page is fully loaded
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loadingScreen');
            loadingScreen.classList.add('hidden');
            setTimeout(() => {
                loadingScreen.remove();
            }, 500);
        });

        async function trackVisitor() {
            try {
                await fetch('{{ route('visitor.track') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({}),
                });
            } catch (error) {
                console.warn('Visitor tracking gagal:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', trackVisitor);

        function bootScrollReveal() {
            const revealItems = document.querySelectorAll('[data-scroll-reveal]');

            if (!revealItems.length) {
                return;
            }

            if (!('IntersectionObserver' in window)) {
                revealItems.forEach((item) => item.classList.add('is-visible'));
                return;
            }

            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, {
                threshold: 0.18,
                rootMargin: '0px 0px -8% 0px',
            });

            revealItems.forEach((item) => observer.observe(item));
        }

        document.addEventListener('DOMContentLoaded', bootScrollReveal);

    </script>
    <script src="{{ asset('js/mobile-fullscreen-nav.js') }}"></script>
</body>
</html>
