<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite('resources/css/home.css')
</head>
<body>
    <nav>
        <div class="mainav">
            <a href="">Home</a>
            <a href="">Artworks</a>
            <a href="">Commissions</a>
            <a href="">Contact</a>
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
    </section>

    <script>
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