<x-layouts::app :title="$course->title">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <a href="{{ route('learning.index') }}" wire:navigate class="w-fit text-sm font-semibold text-espresso underline decoration-terracota decoration-2 underline-offset-4">Volver a mi cuenta</a>

        <h1 class="text-[clamp(28px,3.5vw,40px)] font-bold text-espresso">{{ $course->title }}</h1>

        <div class="prose-none max-w-none text-[17px] leading-[1.7] text-espresso">
            {!! $course->description !!}
        </div>

        @if ($embedUrl)
            <x-vimeo-player :url="$embedUrl" :title="$course->title" />
        @elseif ($course->type === \App\Enums\CourseType::LiveProgram)
            <div class="rounded-sm bg-crema-suave p-6">
                <p class="text-espresso opacity-70">Este programa es en vivo, por eso no tiene un video para ver aquí</p>
            </div>
        @else
            <div class="rounded-sm bg-crema-suave p-6">
                <p class="text-espresso opacity-70">El video de este curso todavía no está disponible</p>
            </div>
        @endif
    </div>
</x-layouts::app>
