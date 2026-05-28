<div class="mobile-fullscreen-nav" id="mobileFullscreenNav" aria-hidden="true">
    <div class="mobile-fullscreen-nav__backdrop" data-mobile-nav-close></div>
    <button class="mobile-fullscreen-nav__close" type="button" aria-label="Close navigation" data-mobile-nav-close>
        <span></span>
        <span></span>
    </button>
    <div class="mobile-fullscreen-nav__content">
        <a href="{{ route('home') }}" class="mobile-fullscreen-nav__link">Home</a>
        <a href="{{ route('artworks') }}" class="mobile-fullscreen-nav__link">Artworks</a>
        <a href="{{ route('commission') }}" class="mobile-fullscreen-nav__link">Commissions</a>
        <a href="{{ route('three_d') }}" class="mobile-fullscreen-nav__link">3D</a>
        <a href="{{ route('contact') }}" class="mobile-fullscreen-nav__link">Contact</a>
    </div>
</div>
