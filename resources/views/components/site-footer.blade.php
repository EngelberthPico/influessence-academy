{{-- CTA FINAL + FOOTER --}}
<section class="bg-espresso-oscuro px-[6vw] pt-16 pb-10 text-crema lg:pt-[100px] lg:pb-[60px]">
    <div class="mb-16 max-w-[640px]">
        <h2 class="mb-5 text-[clamp(30px,4vw,46px)] leading-[1.15] font-bold">Tu carrera de creadora empieza con una decisión, <span class="font-serif font-normal italic">no con una excusa más</span></h2>
        @if (request()->routeIs('courses.index'))
            <a
                href="https://wa.me/14073533200?text={{ urlencode('¡Hola! Me interesa saber más sobre los cursos de Influessence Academy.') }}"
                target="_blank"
                rel="noopener"
                class="mt-3 inline-block rounded-full bg-terracota px-[34px] py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-crema hover:text-espresso hover:shadow-lg"
            >Habla con nosotros</a>
        @else
            <a href="{{ route('courses.index') }}" wire:navigate class="mt-3 inline-block rounded-full bg-terracota px-[34px] py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-crema hover:text-espresso hover:shadow-lg">Explorar cursos</a>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-10 border-t border-crema/20 pt-10 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            <img src="{{ asset('images/brand/logo-crema.png') }}" alt="Influessence Academy" class="mb-3 h-9 w-auto">
            <p class="max-w-[220px] text-[14px] leading-[1.6] opacity-70">Academia online para creadoras hispanas</p>
        </div>

        <div class="flex flex-col gap-3">
            <p class="mb-1 text-xs font-semibold tracking-[0.1em] text-terracota uppercase">Explora</p>
            <a href="{{ route('courses.index') }}" wire:navigate class="text-[14px] text-crema opacity-[0.85]">Cursos</a>
            <a href="{{ route('home') }}#categorias" class="text-[14px] text-crema opacity-[0.85]">Categorías</a>
            <a href="{{ route('home') }}#como-funciona" class="text-[14px] text-crema opacity-[0.85]">Cómo funciona</a>
            <a href="{{ route('home') }}#nosotros" class="text-[14px] text-crema opacity-[0.85]">Nosotros</a>
        </div>

        <div class="flex flex-col gap-3">
            <p class="mb-1 text-xs font-semibold tracking-[0.1em] text-terracota uppercase">Contacto</p>
            <a href="mailto:info@influessenceacademy.com" class="flex items-center gap-2 text-[14px] text-crema opacity-[0.85]">
                <flux:icon.envelope class="size-4 shrink-0" />
                info@influessenceacademy.com
            </a>
            <a href="https://wa.me/14073533200" target="_blank" rel="noopener" class="flex items-center gap-2 text-[14px] text-crema opacity-[0.85]">
                <svg viewBox="0 0 24 24" fill="currentColor" class="size-4 shrink-0" aria-hidden="true">
                    <path d="M12 2C6.48 2 2 6.48 2 12c0 1.89.52 3.66 1.42 5.18L2 22l4.94-1.3A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm0 18c-1.6 0-3.1-.44-4.38-1.2l-.31-.19-2.93.77.78-2.86-.2-.3A7.94 7.94 0 0 1 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8-3.59 8-8 8Zm4.36-5.96c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.94-1.2-.72-.64-1.2-1.44-1.34-1.68-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.4-.54-.41h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.42-.58 1.62-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z"/>
                </svg>
                +1 (407) 353-3200
            </a>
        </div>

        <div class="flex flex-col gap-3">
            <p class="mb-1 text-xs font-semibold tracking-[0.1em] text-terracota uppercase">Síguenos</p>
            <a href="https://www.instagram.com/influessenceacademy/" target="_blank" rel="noopener" class="flex items-center gap-2 text-[14px] text-crema opacity-[0.85]">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="size-4 shrink-0" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="5" />
                    <circle cx="12" cy="12" r="4" />
                    <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor" stroke="none" />
                </svg>
                @influessenceacademy
            </a>
            <a href="https://influessenceagency.com" target="_blank" rel="noopener" class="mt-2 text-[14px] font-semibold text-terracota">Conoce Influessence Agency</a>
        </div>
    </div>

    <div class="mt-10 border-t border-crema/20 pt-6 text-[13px] opacity-60">
        © {{ date('Y') }} Influessence Academy. Todos los derechos reservados
    </div>
</section>

{{-- Botón flotante de WhatsApp --}}
<a
    href="https://wa.me/14073533200?text={{ urlencode('¡Hola! Me interesa saber más sobre los cursos de Influessence Academy.') }}"
    target="_blank"
    rel="noopener"
    aria-label="Escríbenos por WhatsApp"
    class="fixed right-6 bottom-6 z-50 flex size-14 items-center justify-center rounded-full bg-terracota text-crema shadow-lg transition hover:brightness-110"
>
    <svg viewBox="0 0 24 24" fill="currentColor" class="size-7" aria-hidden="true">
        <path d="M12 2C6.48 2 2 6.48 2 12c0 1.89.52 3.66 1.42 5.18L2 22l4.94-1.3A9.94 9.94 0 0 0 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2Zm0 18c-1.6 0-3.1-.44-4.38-1.2l-.31-.19-2.93.77.78-2.86-.2-.3A7.94 7.94 0 0 1 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8-3.59 8-8 8Zm4.36-5.96c-.24-.12-1.42-.7-1.64-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.94-1.2-.72-.64-1.2-1.44-1.34-1.68-.14-.24-.02-.37.1-.49.11-.11.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.47-.4-.4-.54-.41h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.11.16 1.53.1.47-.07 1.42-.58 1.62-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z"/>
    </svg>
</a>
