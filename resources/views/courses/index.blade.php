<x-layouts.marketing>
    <section class="px-[6vw] pt-8 pb-16 lg:pt-12 lg:pb-[100px]">
        <h1 class="mb-5 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Elige tu nivel de acompañamiento</h1>
        <p class="mb-12 max-w-[560px] text-base opacity-70">Desde una sesión puntual hasta seis meses de mentoría — el nivel de cercanía con Fabi lo eliges tú.</p>

        @if ($liveProgramCourses->isEmpty() && $hybridCourses->isEmpty() && $recordedCourses->isEmpty())
            <p class="text-base opacity-70">Todavía no hay cursos publicados. Vuelve pronto.</p>
        @endif

        @if ($liveProgramCourses->isNotEmpty())
            <p class="mb-8 max-w-[560px] text-base opacity-70">Acompañamiento en vivo con Fabi</p>
            <div class="mb-18 grid grid-cols-1 gap-6 lg:grid-cols-3">
                @foreach ($liveProgramCourses as $course)
                    <a href="{{ route('courses.show', $course) }}" wire:navigate
                       class="flex flex-col gap-4 bg-crema-suave p-8 pt-6 {{ $loop->last && $liveProgramCourses->count() > 1 ? 'border-t-4 border-terracota' : 'border-t-4 border-transparent' }}">
                        <span class="w-fit rounded-full bg-crema px-3 py-1 text-xs font-semibold tracking-wide text-espresso uppercase">{{ $course->duration_label }}</span>
                        <h3 class="text-xl font-bold">{{ $course->title }}</h3>
                        <p class="grow max-w-[240px] text-[15px] leading-[1.6] opacity-[0.85]">{{ Str::limit(strip_tags($course->description), 120) }}</p>
                        <div class="flex items-center justify-between border-t border-espresso/10 pt-4">
                            @if ($course->price_cents > 0)
                                <span class="text-xl font-bold text-terracota">${{ number_format($course->price_cents / 100, 0) }}</span>
                            @else
                                <span class="text-sm font-semibold opacity-60">Precio próximamente</span>
                            @endif
                            <span class="text-sm font-semibold underline decoration-terracota decoration-2 underline-offset-4">Ver detalles</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($hybridCourses->isNotEmpty())
            <p class="mb-8 max-w-[560px] text-base opacity-70">Curso grabado con acompañamiento final</p>
            <div class="mb-18 grid grid-cols-1 gap-6">
                @foreach ($hybridCourses as $course)
                    <a href="{{ route('courses.show', $course) }}" wire:navigate
                       class="flex flex-col gap-5 border-t-4 border-terracota bg-crema-suave p-9">
                        <span class="w-fit rounded-full bg-crema px-3 py-1 text-xs font-semibold tracking-wide text-espresso uppercase">Curso + asesoría</span>
                        <div class="flex flex-col items-start gap-6 sm:flex-row sm:items-end sm:justify-between">
                            <div class="max-w-[560px]">
                                <h3 class="mb-3 text-[22px] font-bold">{{ $course->title }}</h3>
                                <p class="text-[15px] leading-[1.6] opacity-[0.85]">{{ Str::limit(strip_tags($course->description), 160) }}</p>
                            </div>
                            <div class="flex shrink-0 flex-col items-start gap-3 sm:items-end">
                                @if ($course->price_cents > 0)
                                    <span class="text-[22px] font-bold text-terracota">${{ number_format($course->price_cents / 100, 0) }}</span>
                                @else
                                    <span class="text-sm font-semibold opacity-60">Precio próximamente</span>
                                @endif
                                <span class="text-sm font-semibold underline decoration-terracota decoration-2 underline-offset-4">Ver detalles</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($recordedCourses->isNotEmpty())
            <p class="mb-8 max-w-[560px] text-base opacity-70">Catálogo de cursos grabados</p>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-5">
                @foreach ($recordedCourses as $course)
                    <a href="{{ route('courses.show', $course) }}" wire:navigate
                       class="flex flex-col gap-3 bg-crema-suave p-[26px]">
                        <span class="w-fit rounded-full bg-crema px-3 py-1 text-xs font-semibold tracking-wide text-espresso uppercase">Grabado</span>
                        <h3 class="text-[17px] font-bold">{{ $course->title }}</h3>
                        <p class="grow text-sm leading-[1.55] opacity-[0.85]">{{ Str::limit(strip_tags($course->description), 110) }}</p>
                        <div class="flex items-center justify-between border-t border-espresso/10 pt-3">
                            @if ($course->price_cents > 0)
                                <span class="text-lg font-bold text-terracota">${{ number_format($course->price_cents / 100, 0) }}</span>
                            @else
                                <span class="text-xs font-semibold opacity-60">Precio próximamente</span>
                            @endif
                            <span class="text-sm font-semibold underline decoration-terracota decoration-2 underline-offset-4">Ver curso</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.marketing>
