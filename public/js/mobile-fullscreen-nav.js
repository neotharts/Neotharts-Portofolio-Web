(function () {
    'use strict';

    function bootMobileFullscreenNav() {
        const breakpoint = window.matchMedia('(max-width: 1024px)');
        const hamburger = document.querySelector('.mainavmobile');
        const overlay = document.getElementById('mobileFullscreenNav');

        if (!hamburger || !overlay) {
            return;
        }

        const closeTriggers = overlay.querySelectorAll('[data-mobile-nav-close], .mobile-fullscreen-nav__link');
        let closeTimer = null;

        function isOpen() {
            return overlay.classList.contains('is-open');
        }

        function openNav() {
            if (!breakpoint.matches || isOpen()) {
                return;
            }

            window.clearTimeout(closeTimer);
            overlay.classList.remove('is-closing');
            overlay.classList.add('is-open');
            overlay.setAttribute('aria-hidden', 'false');
            hamburger.setAttribute('aria-expanded', 'true');
            document.body.classList.add('mobile-nav-open');

            window.setTimeout(function () {
                overlay.classList.add('is-menu-visible');
            }, 260);
        }

        function closeNav() {
            if (!isOpen()) {
                return;
            }

            overlay.classList.remove('is-menu-visible');
            overlay.classList.add('is-closing');
            hamburger.setAttribute('aria-expanded', 'false');

            closeTimer = window.setTimeout(function () {
                overlay.classList.remove('is-open', 'is-closing');
                overlay.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('mobile-nav-open');
            }, 420);
        }

        hamburger.setAttribute('role', 'button');
        hamburger.setAttribute('tabindex', '0');
        hamburger.setAttribute('aria-controls', 'mobileFullscreenNav');
        hamburger.setAttribute('aria-expanded', 'false');

        hamburger.addEventListener('click', openNav);
        hamburger.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openNav();
            }
        });

        closeTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', closeNav);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeNav();
            }
        });

        function handleBreakpointChange(event) {
            if (!event.matches) {
                closeNav();
            }
        }

        if (typeof breakpoint.addEventListener === 'function') {
            breakpoint.addEventListener('change', handleBreakpointChange);
        } else if (typeof breakpoint.addListener === 'function') {
            breakpoint.addListener(handleBreakpointChange);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootMobileFullscreenNav);
    } else {
        bootMobileFullscreenNav();
    }
}());
