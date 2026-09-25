@props(['transparent' => false])

<nav
    x-data="{ mobileMenuOpen: false }"
    @keydown.escape.window="mobileMenuOpen = false"
    @click.outside="mobileMenuOpen = false"
    class="fixed inset-x-0 top-0 z-50 grid grid-cols-[auto_1fr_auto] items-center gap-4 px-[6vw] py-4 transition-colors duration-300 {{ $transparent ? '' : 'bg-crema' }}"
>
    <div class="flex items-center gap-3">
        <button
            type="button"
            @click="mobileMenuOpen = ! mobileMenuOpen"
            :aria-expanded="mobileMenuOpen.toString()"
            aria-controls="mobile-nav-panel"
            :aria-label="mobileMenuOpen ? 'Cerrar menú' : 'Abrir menú'"
            class="-m-2.5 flex size-11 shrink-0 items-center justify-center text-espresso lg:hidden"
        >
            <flux:icon.bars-3 x-show="! mobileMenuOpen" class="size-6" />
            <flux:icon.x-mark x-show="mobileMenuOpen" x-cloak class="size-6" />
        </button>

        <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
            <img src="{{ asset('images/brand/logo-espresso.png') }}" alt="Influessence" class="h-8 w-auto">
            <x-diamond />
            <span class="font-serif text-xl text-espresso italic">Academy</span>
        </a>
    </div>

    <div class="hidden items-center justify-center gap-7 lg:flex">
        <a href="{{ route('courses.index') }}" wire:navigate class="text-[15px] font-medium text-espresso">Cursos</a>
        <a href="{{ route('home') }}#categorias" class="text-[15px] font-medium text-espresso">Categorías</a>
        <a href="{{ route('home') }}#como-funciona" class="text-[15px] font-medium text-espresso">Cómo funciona</a>
        <a href="{{ route('home') }}#nosotros" class="text-[15px] font-medium text-espresso">Nosotros</a>
        @auth
            @if (auth()->user()->isAdmin())
                <a href="{{ route('filament.admin.resources.course-accesses.index') }}" class="text-[15px] font-medium text-espresso">Administración</a>
            @endif
        @endauth
    </div>

    <div class="flex items-center justify-end gap-5">
        @auth
            <a href="{{ route('learning.index') }}" wire:navigate class="hidden text-[15px] sm:inline {{ request()->routeIs('learning.*') ? 'font-semibold' : 'font-medium' }} text-espresso">Mi cuenta</a>

            <flux:dropdown position="bottom" align="end">
                <flux:profile :initials="auth()->user()->initials()" :chevron="false" aria-label="Cuenta y ajustes" />

                <flux:menu>
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                            <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                        </div>
                    </div>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:menu.item
                                as="button"
                                type="submit"
                                icon="arrow-right-start-on-rectangle"
                                class="w-full cursor-pointer"
                                data-test="logout-button"
                            >
                                {{ __('Log out') }}
                            </flux:menu.item>
                        </form>
                    </flux:menu.radio.group>
                </flux:menu>
            </flux:dropdown>
        @else
            <a href="{{ route('login') }}" wire:navigate class="hidden text-[15px] font-medium text-espresso sm:inline">Entrar</a>
        @endauth

        <a href="{{ route('courses.index') }}" wire:navigate class="hidden shrink-0 rounded-full bg-terracota px-[26px] py-3 text-[15px] font-semibold whitespace-nowrap text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg sm:inline-block">Quiero crecer</a>
    </div>

    {{-- Panel mobile: mismos links que el centro del nav en desktop, más el estado de sesión --}}
    <div
        id="mobile-nav-panel"
        x-show="mobileMenuOpen"
        x-transition
        x-cloak
        class="col-span-3 -mx-[6vw] mt-4 flex flex-col gap-1 border-t border-espresso/10 bg-crema px-[6vw] pt-4 pb-4 shadow-lg lg:hidden"
    >
        <a href="{{ route('courses.index') }}" wire:navigate @click="mobileMenuOpen = false" class="rounded-sm px-3 py-3 text-base font-medium text-espresso hover:bg-crema-suave">Cursos</a>
        <a href="{{ route('home') }}#categorias" @click="mobileMenuOpen = false" class="rounded-sm px-3 py-3 text-base font-medium text-espresso hover:bg-crema-suave">Categorías</a>
        <a href="{{ route('home') }}#como-funciona" @click="mobileMenuOpen = false" class="rounded-sm px-3 py-3 text-base font-medium text-espresso hover:bg-crema-suave">Cómo funciona</a>
        <a href="{{ route('home') }}#nosotros" @click="mobileMenuOpen = false" class="rounded-sm px-3 py-3 text-base font-medium text-espresso hover:bg-crema-suave">Nosotros</a>

        <div class="my-2 h-px bg-espresso/10"></div>

        @auth
            <a href="{{ route('learning.index') }}" wire:navigate @click="mobileMenuOpen = false" class="rounded-sm px-3 py-3 text-base font-semibold text-espresso hover:bg-crema-suave">Mi cuenta</a>

            @if (auth()->user()->isAdmin())
                <a href="{{ route('filament.admin.resources.course-accesses.index') }}" @click="mobileMenuOpen = false" class="rounded-sm px-3 py-3 text-base font-medium text-espresso hover:bg-crema-suave">Administración</a>
            @endif
        @else
            <a href="{{ route('login') }}" wire:navigate @click="mobileMenuOpen = false" class="rounded-sm px-3 py-3 text-base font-medium text-espresso hover:bg-crema-suave">Entrar</a>
        @endauth
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
