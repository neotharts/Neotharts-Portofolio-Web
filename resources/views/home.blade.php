<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite('resources/css/home.css')
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
            <a href="/">Home</a>
            <a href="/artworks">Artworks</a>
            <a href="/commissions">Commissions</a>
            <a href="/contact">Contact</a>
        </div>
        <div class="mainavmobile">
            <span class="material-icons">menu</span>
        </div>
        </nav>

    <header>
        <div class="img">
            <img src="{{ asset('img/hello.png') }}" alt="Hello">
        </div>
        <div class="mainheader">
            <img class="desktop" src="{{ asset('img/header_pc_web.png') }}" alt="Main Header">
            <img class="mobile" src="{{ asset('img/header_mobile_web.png') }}" alt="Main Header">
            <div class="bottommore">
                <a href="#latest"> See more </a>
            </div>
        </div>
    </header>
    <section id="latest">
        <div class="text">
            <img class="desktop" src="{{ asset('img/latest_text.png') }}" alt="Latest Artworks">
            <img class="mobile" src="{{ asset('img/latest_text_mobile.png') }}" alt="Latest Artwork">
        </div>
        <div class="placeholders">
            @if($artworks->count() > 0)
                @foreach($artworks as $artwork)
                    <div class="placeholder">
                        @if($artwork->image)
                            <img src="{{ asset('storage/' . $artwork->image) }}" alt="{{ $artwork->title }}">
                        @else
                            <div class="placeholder-empty">No Image</div>
                        @endif
                    </div>
                @endforeach

                @for($i = $artworks->count(); $i < 3; $i++)
                    <div class="placeholder placeholder-empty">
                        <span>Artwork Coming Soon</span>
                    </div>
                @endfor
            @else
                @for($i = 0; $i < 3; $i++)
                    <div class="placeholder placeholder-empty">
                        <span>Artwork Coming Soon</span>
                    </div>
                @endfor
            @endif

            <div class="kotak"></div>
        </div>
        <div class="bottommore">
                <a href="/artworks"> See more </a>
        </div>
    </section>

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
    </script>
</body>
</html>