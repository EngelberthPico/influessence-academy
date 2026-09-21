@props(['course', 'current' => null])

<nav aria-label="Contenido del curso" class="flex flex-col gap-6 rounded-sm border-t-4 border-terracota bg-crema-suave p-6">
    @foreach ($course->modules as $moduleIndex => $module)
        <div class="flex flex-col gap-3">
            <div>
                <p class="text-xs font-semibold tracking-wide text-terracota uppercase">Módulo {{ $moduleIndex + 1 }}</p>
                <h3 class="font-semibold text-espresso">{{ $module->title }}</h3>
                @if ($module->description)
                    <p class="text-sm text-espresso opacity-70">{{ $module->description }}</p>
                @endif
            </div>

            <ul class="flex flex-col gap-1">
                @foreach ($module->lessons as $lessonIndex => $lesson)
                    @php $isCurrent = $current && $current->is($lesson); @endphp
                    <li>
                        <a
                            href="{{ route('learning.lesson', [$course, $lesson]) }}"
                            wire:navigate
                            @if ($isCurrent) aria-current="page" @endif
                            class="block rounded-sm px-3 py-2 text-sm text-espresso {{ $isCurrent ? 'bg-espresso/10 font-semibold' : 'hover:bg-espresso/5' }}"
                        >{{ $lessonIndex + 1 }}. {{ $lesson->title }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</nav>
