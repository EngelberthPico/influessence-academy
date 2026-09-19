@props(['transparentNav' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="overflow-x-hidden bg-crema font-sans text-espresso antialiased">
        {{-- NAV --}}
        <nav class="fixed inset-x-0 top-0 z-50 grid grid-cols-[auto_1fr_auto] items-center gap-4 px-[6vw] py-4 transition-colors duration-300 {{ $transparentNav ? '' : 'bg-crema' }}">
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                <img src="{{ asset('images/brand/logo-espresso.png') }}" alt="Influessence" class="h-8 w-auto">
                <svg class="size-3.5 shrink-0 text-terracota" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 0C12.6 6.7 17.3 11.4 24 12C17.3 12.6 12.6 17.3 12 24C11.4 17.3 6.7 12.6 0 12C6.7 11.4 11.4 6.7 12 0Z" />
                </svg>
                <span class="font-serif text-xl text-espresso italic">Academy</span>
            </a>
            <div class="hidden items-center justify-center gap-7 lg:flex">
                <a href="{{ route('courses.index') }}" wire:navigate class="text-[15px] font-medium text-espresso">Cursos</a>
                <a href="{{ route('home') }}#categorias" class="text-[15px] font-medium text-espresso">Categorías</a>
                <a href="{{ route('home') }}#como-funciona" class="text-[15px] font-medium text-espresso">Cómo funciona</a>
                <a href="{{ route('home') }}#nosotras" class="text-[15px] font-medium text-espresso">Nosotras</a>
            </div>
            <div class="flex items-center justify-end gap-6">
                <a href="{{ route('login') }}" wire:navigate class="hidden text-[15px] font-medium text-espresso sm:inline">Entrar</a>
                <a href="{{ route('courses.index') }}" wire:navigate class="shrink-0 rounded-full bg-terracota px-[26px] py-3 text-[15px] font-semibold whitespace-nowrap text-crema">Quiero crecer</a>
            </div>
        </nav>

        <main class="{{ $transparentNav ? '' : 'pt-20' }}">
            {{ $slot }}
        </main>

        {{-- CTA FINAL + FOOTER --}}
        <section class="bg-espresso-oscuro px-[6vw] pt-16 pb-10 text-crema lg:pt-[100px] lg:pb-[60px]">
            <div class="mb-20 max-w-[640px]">
                <h2 class="mb-5 text-[clamp(30px,4vw,46px)] leading-[1.15] font-bold">Tu carrera de creadora empieza con una decisión, no con una excusa más.</h2>
                <a href="{{ route('courses.index') }}" wire:navigate class="mt-3 inline-block rounded-full bg-terracota px-[34px] py-4 text-base font-semibold text-crema">Explorar cursos</a>
            </div>
            <div class="flex flex-wrap justify-between gap-8 border-t border-crema/20 pt-8">
                <div>
                    <img src="{{ asset('images/brand/logo-crema.png') }}" alt="Influessence Academy" class="mb-2 h-10 w-auto">
                    <p class="text-[14px] opacity-70">Academia online para creadoras hispanas en Estados Unidos.</p>
                </div>
                <div class="flex flex-wrap gap-8">
                    <a href="{{ route('courses.index') }}" wire:navigate class="text-[14px] text-crema opacity-[0.85]">Cursos</a>
                    <a href="{{ route('home') }}#categorias" class="text-[14px] text-crema opacity-[0.85]">Categorías</a>
                    <a href="{{ route('home') }}#nosotras" class="text-[14px] text-crema opacity-[0.85]">Nosotras</a>
                    <a href="https://influessenceagency.com" target="_blank" rel="noopener" class="text-[14px] font-semibold text-terracota">Influessence Agency, para negocios</a>
                </div>
            </div>
        </section>

        <script>
            (function () {
                const nav = document.querySelector('nav');
                const transparentNav = @json($transparentNav);
                const onScroll = () => {
                    nav.classList.toggle(transparentNav ? 'bg-crema' : 'shadow-sm', window.scrollY > 8);
                };
                onScroll();
                window.addEventListener('scroll', onScroll, { passive: true });
            })();
        </script>
    </body>
</html>
