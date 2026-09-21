<x-layouts::app :title="'Mi cuenta'">
    <div class="flex h-full w-full flex-1 flex-col gap-8">
        <h1 class="text-[clamp(28px,3.5vw,40px)] font-bold text-espresso">Mi cuenta</h1>

        <div class="flex flex-col gap-4">
            <h2 class="text-xl font-bold text-espresso">Mis cursos</h2>

            @if ($courses->isEmpty())
                <div class="flex flex-col items-start gap-4 rounded-sm bg-crema-suave p-8">
                    <p class="text-espresso opacity-70">Todavía no tienes cursos</p>
                    <a href="{{ route('courses.index') }}" wire:navigate class="shrink-0 rounded-full bg-terracota px-[26px] py-3 text-[15px] font-semibold whitespace-nowrap text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Explorar cursos</a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($courses as $course)
                        <div class="flex flex-col gap-3 rounded-sm border border-espresso/15 border-t-4 border-t-terracota bg-crema-suave p-[26px] transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                            <span class="w-fit text-xs font-semibold tracking-wide uppercase text-terracota">{{ $course->duration_label }}</span>
                            <h3 class="text-[17px] font-bold text-espresso">{{ $course->title }}</h3>
                            <a href="{{ route('learning.show', $course) }}" wire:navigate class="mt-2 w-fit text-sm font-semibold text-espresso underline decoration-terracota decoration-2 underline-offset-4">Ver curso</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>
