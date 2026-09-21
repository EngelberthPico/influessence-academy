@props(['transparent' => false])

<nav class="fixed inset-x-0 top-0 z-50 grid grid-cols-[auto_1fr_auto] items-center gap-4 px-[6vw] py-4 transition-colors duration-300 {{ $transparent ? '' : 'bg-crema' }}">
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
        <a href="{{ route('home') }}#nosotros" class="text-[15px] font-medium text-espresso">Nosotros</a>
    </div>
    <div class="flex items-center justify-end gap-6">
        @auth
            <a href="{{ route('learning.index') }}" wire:navigate class="text-[15px] text-espresso {{ request()->routeIs('learning.*') ? 'font-semibold' : 'font-medium' }}">Mi cuenta</a>
        @else
            <a href="{{ route('login') }}" wire:navigate class="hidden text-[15px] font-medium text-espresso sm:inline">Entrar</a>
        @endauth
        <a href="{{ route('courses.index') }}" wire:navigate class="shrink-0 rounded-full bg-terracota px-[26px] py-3 text-[15px] font-semibold whitespace-nowrap text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg @auth hidden sm:inline-block @endauth">Quiero crecer</a>
    </div>
</nav>

<script>
    (function () {
        const nav = document.querySelector('nav');
        const transparentNav = @json($transparent);
        const onScroll = () => {
            nav.classList.toggle(transparentNav ? 'bg-crema' : 'shadow-sm', window.scrollY > 8);
        };
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
    })();
</script>
