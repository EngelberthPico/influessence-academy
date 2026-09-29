<x-layouts.marketing>
    <section class="flex flex-col items-center px-[6vw] py-24 text-center lg:py-[140px]">
        @if ($summary)
            @php
                $courseNames = $summary['courses']->pluck('title')->join(', ', ' y ');
            @endphp

            @auth
                <h1 class="mb-5 max-w-[560px] text-[clamp(28px,3.5vw,40px)] font-bold">¡Listo! Ya tienes acceso a {{ $courseNames }}</h1>
                <p class="max-w-[480px] text-base leading-[1.6] opacity-80">Pagaste ${{ number_format($summary['totalCents'] / 100, 0) }}. Ya puedes empezar cuando quieras.</p>

                @if ($summary['courses']->count() === 1)
                    <a href="{{ route('learning.show', $summary['courses']->first()) }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Ir a mi curso</a>
                @else
                    <a href="{{ route('learning.index') }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Ir a mi cuenta</a>
                @endif
            @else
                <h1 class="mb-5 max-w-[560px] text-[clamp(28px,3.5vw,40px)] font-bold">¡Gracias por tu compra de {{ $courseNames }}!</h1>
                <p class="max-w-[480px] text-base leading-[1.6] opacity-80">
                    Pagaste ${{ number_format($summary['totalCents'] / 100, 0) }}.
                    @if ($summary['email'])
                        Revisa el correo <strong>{{ $summary['email'] }}</strong> para poner tu contraseña y ver tu contenido.
                    @else
                        Revisa tu correo para poner tu contraseña y ver tu contenido.
                    @endif
                    Puede tardar unos minutos en llegar.
                </p>
                <a href="{{ route('home') }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Volver al inicio</a>
            @endauth
        @else
            <h1 class="mb-5 max-w-[560px] text-[clamp(28px,3.5vw,40px)] font-bold">¡Gracias por tu compra!</h1>
            <p class="max-w-[480px] text-base leading-[1.6] opacity-80">Revisa tu correo para acceder a tu cuenta y empezar tu curso. Puede tardar unos minutos en llegar.</p>
            <a href="{{ route('home') }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Volver al inicio</a>
        @endif
    </section>
</x-layouts.marketing>
