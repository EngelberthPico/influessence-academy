<x-layouts.marketing>
    <section class="px-[6vw] pt-8 pb-16 lg:pt-12 lg:pb-[100px]">
        <h1 class="mb-10 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Tu carrito</h1>

        @if (session('error'))
            <p class="mb-8 max-w-[600px] rounded-sm bg-terracota/10 px-6 py-4 text-sm font-semibold text-terracota">{{ session('error') }}</p>
        @endif

        @if ($courses->isEmpty())
            <p class="max-w-[480px] text-base leading-[1.6] opacity-70">
                Tu carrito está vacío. <a href="{{ route('courses.index') }}" wire:navigate class="font-semibold text-terracota">Explora los cursos</a> y agrega el que más te convenga.
            </p>
        @else
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-[1.4fr_1fr]">
                <div class="flex flex-col gap-5">
                    @foreach ($courses as $course)
                        <div class="reveal flex items-center justify-between gap-6 rounded-sm border border-espresso/15 bg-crema-suave p-6">
                            <div>
                                <p class="mb-1 text-xs font-semibold tracking-[0.1em] text-terracota uppercase">{{ $course->card_label }}</p>
                                <h2 class="text-lg font-bold">{{ $course->title }}</h2>
                            </div>
                            <div class="flex shrink-0 items-center gap-5">
                                <span class="text-lg font-bold text-terracota">${{ number_format($course->price_cents / 100, 0) }}</span>
                                <form method="POST" action="{{ route('cart.remove', $course) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-espresso opacity-60 transition hover:opacity-100">Quitar</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside class="reveal h-fit rounded-sm bg-crema-suave p-8">
                    <div class="mb-6 flex items-center justify-between border-b border-espresso/10 pb-6">
                        <span class="text-base font-semibold">Total</span>
                        <span class="text-2xl font-bold text-terracota">${{ number_format($totalCents / 100, 0) }}</span>
                    </div>

                    <form method="POST" action="{{ route('cart.pay') }}">
                        @csrf
                        <button type="submit" class="w-full rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">
                            Pagar con tarjeta
                        </button>
                    </form>

                    <p class="mt-4 text-center text-xs text-espresso opacity-60">Todas las ventas son finales. <a href="{{ route('legal.refunds') }}" wire:navigate class="font-semibold text-terracota">Ver política de reembolsos</a>.</p>
                </aside>
            </div>
        @endif
    </section>
</x-layouts.marketing>
