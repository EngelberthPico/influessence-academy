<x-layouts.marketing>
    <section class="px-[6vw] pt-8 pb-16 lg:pt-12 lg:pb-[100px]">
        <a href="{{ route('courses.index') }}" wire:navigate class="mb-8 inline-block text-sm font-semibold text-terracota">← Volver al catálogo</a>

        <div class="grid grid-cols-1 gap-12 lg:grid-cols-[1.4fr_1fr]">
            <div class="reveal">
                <p class="mb-2 text-xs font-semibold tracking-[0.12em] text-terracota uppercase">{{ $course->type->getLabel() }}</p>
                <h1 class="mb-6 text-[clamp(28px,4vw,44px)] leading-[1.15] font-bold">{{ $course->title }}</h1>
                <div class="prose-none max-w-none text-[17px] leading-[1.7] text-espresso opacity-90">
                    {!! $course->description !!}
                </div>
            </div>

            <aside class="reveal h-fit rounded-sm bg-crema-suave p-8">
                @if ($course->price_cents > 0)
                    <div class="mb-6 text-3xl font-bold text-terracota">${{ number_format($course->price_cents / 100, 0) }}</div>
                @else
                    <div class="mb-6 text-lg font-semibold opacity-60">Precio próximamente</div>
                @endif

                <ul class="mb-8 flex flex-col gap-3 text-sm text-espresso opacity-90">
                    @if ($course->session_count)
                        <li>{{ $course->session_count }} {{ $course->session_count === 1 ? 'sesión en vivo' : 'sesiones en vivo' }}</li>
                    @endif
                    @if ($course->duration_months)
                        <li>Duración: {{ $course->duration_months }} {{ $course->duration_months === 1 ? 'mes' : 'meses' }}</li>
                    @endif
                    @if ($course->redemption_window_days)
                        <li>Asesoría redimible dentro de {{ $course->redemption_window_days }} días desde la compra</li>
                    @endif
                </ul>

                {{-- TODO: conectar a Stripe Checkout cuando esa integración esté lista. Por ahora el botón no procesa pago. --}}
                <button type="button" disabled class="w-full cursor-not-allowed rounded-full bg-espresso/40 px-9 py-4 text-base font-semibold text-crema">
                    Comprar (próximamente)
                </button>
            </aside>
        </div>
    </section>
</x-layouts.marketing>
