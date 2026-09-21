<x-layouts::app :title="$lesson->title">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('learning.index') }}" wire:navigate class="w-fit text-sm font-semibold text-espresso underline decoration-terracota decoration-2 underline-offset-4">Volver a mi cuenta</a>
            <a href="{{ route('learning.show', $course) }}" wire:navigate class="w-fit text-sm font-semibold text-espresso underline decoration-terracota decoration-2 underline-offset-4">{{ $course->title }}</a>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div class="flex flex-col gap-6 lg:col-span-2">
                <h1 class="text-[clamp(28px,3.5vw,40px)] font-bold text-espresso">{{ $lesson->title }}</h1>

                @if ($embedUrl)
                    <x-vimeo-player :url="$embedUrl" :title="$lesson->title" />
                @else
                    <div class="rounded-sm bg-crema-suave p-6">
                        <p class="text-espresso opacity-70">El video todavía no está disponible</p>
                    </div>
                @endif

                @if ($lesson->description)
                    <p class="max-w-none text-[17px] leading-[1.7] whitespace-pre-line text-espresso">{{ $lesson->description }}</p>
                @endif

                <div class="flex items-center gap-6 text-sm font-semibold">
                    @if ($previous)
                        <a href="{{ route('learning.lesson', [$course, $previous]) }}" wire:navigate class="text-espresso underline decoration-terracota decoration-2 underline-offset-4">Anterior</a>
                    @endif

                    @if ($next)
                        <a href="{{ route('learning.lesson', [$course, $next]) }}" wire:navigate class="text-espresso underline decoration-terracota decoration-2 underline-offset-4">Siguiente</a>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-[var(--nav-height)]">
                    <x-course-outline :course="$course" :current="$lesson" />
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
