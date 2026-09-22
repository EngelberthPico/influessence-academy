@props(['transparentNav' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="overflow-x-hidden bg-crema font-sans text-espresso antialiased">
        <x-site-nav :transparent="$transparentNav" />

        <main class="{{ $transparentNav ? '' : 'pt-[var(--nav-height)]' }}">
            {{ $slot }}
        </main>

        <x-site-footer />

        <script>
            (function () {
                const revealEls = document.querySelectorAll('.reveal');
                if (!revealEls.length || !('IntersectionObserver' in window)) {
                    revealEls.forEach((el) => el.classList.add('is-visible'));
                    return;
                }
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });
                revealEls.forEach((el) => observer.observe(el));
            })();
        </script>

        @fluxScripts
    </body>
</html>
