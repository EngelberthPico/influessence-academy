<x-layouts.marketing>
    <section class="flex flex-col items-center px-[6vw] py-24 text-center lg:py-[140px]">
        @if ($summary)
            @php
                $courseNames = $summary['courses']->pluck('title')->join(', ', ' y ');
            @endphp

            @auth
                @if ($accessGranted)
                    <h1 class="mb-5 max-w-[560px] text-[clamp(28px,3.5vw,40px)] font-bold">¡Listo! Ya tienes acceso a {{ $courseNames }}</h1>
                    <p class="max-w-[480px] text-base leading-[1.6] opacity-80">Pagaste ${{ number_format($summary['totalCents'] / 100, 0) }}. Ya puedes empezar cuando quieras.</p>

                    @if ($summary['courses']->count() === 1)
                        <a href="{{ route('learning.show', $summary['courses']->first()) }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Ir a mi curso</a>
                    @else
                        <a href="{{ route('learning.index') }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Ir a mi cuenta</a>
                    @endif
                @else
                    <h1 class="mb-5 max-w-[560px] text-[clamp(28px,3.5vw,40px)] font-bold">¡Pago recibido!</h1>
                    <p class="max-w-[480px] text-base leading-[1.6] opacity-80">Estamos activando tu acceso a {{ $courseNames }}. Suele tardar unos segundos. Esta página se actualiza sola.</p>
                    <p class="mt-3 max-w-[480px] text-sm leading-[1.6] opacity-70">¿Pasaron unos minutos y no lo ves en Mi cuenta? Escríbenos a info@influessenceacademy.com.</p>
                    <a href="{{ route('learning.index') }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Ir a mi cuenta</a>

                    <div
                        x-data
                        x-init="
                            const sessionId = new URLSearchParams(location.search).get('session_id') ?? 'sin-sesion';
                            const key = 'thanksReloadCount:' + sessionId;
                            const attempts = parseInt(sessionStorage.getItem(key) ?? '0', 10);
                            if (attempts < 6) {
                                sessionStorage.setItem(key, String(attempts + 1));
                                setTimeout(() => location.reload(), 5000);
                            }
                        "
                    ></div>
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
                <p class="mt-3 max-w-[480px] text-sm leading-[1.6] opacity-70">¿No te llegó? Usa <a href="{{ route('password.request') }}" wire:navigate class="font-semibold text-terracota">Olvidé mi contraseña</a> con el mismo correo de tu compra.</p>
                <a href="{{ route('home') }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Volver al inicio</a>
            @endauth
        @else
            <h1 class="mb-5 max-w-[560px] text-[clamp(28px,3.5vw,40px)] font-bold">¡Gracias por tu compra!</h1>
            <p class="max-w-[480px] text-base leading-[1.6] opacity-80">Revisa tu correo para acceder a tu cuenta y empezar tu curso. Puede tardar unos minutos en llegar.</p>
            <a href="{{ route('home') }}" wire:navigate class="mt-9 inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Volver al inicio</a>
        @endif
    </section>
</x-layouts.marketing>
