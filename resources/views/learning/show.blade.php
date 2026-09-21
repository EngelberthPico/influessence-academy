<x-layouts::app :title="$course->title">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <a href="{{ route('learning.index') }}" wire:navigate class="w-fit text-sm font-semibold text-espresso underline decoration-terracota decoration-2 underline-offset-4">Volver a mi cuenta</a>

        <h1 class="text-[clamp(28px,3.5vw,40px)] font-bold text-espresso">{{ $course->title }}</h1>

        <div class="prose-none max-w-none text-[17px] leading-[1.7] text-espresso">
            {!! $course->description !!}
        </div>

        @if ($embedUrl)
            <x-vimeo-player :url="$embedUrl" :title="$course->title" />
        @elseif ($course->type !== \App\Enums\CourseType::LiveProgram && $course->modules->isEmpty())
            <div class="rounded-sm bg-crema-suave p-6">
                <p class="text-espresso opacity-70">El video de este curso todavía no está disponible</p>
            </div>
        @endif

        @if ($course->type === \App\Enums\CourseType::LiveProgram)
            <div class="flex flex-col gap-4 rounded-sm border border-espresso/15 bg-crema-suave p-6">
                <div>
                    <h2 class="text-xl font-bold text-espresso">Agenda tu clase</h2>
                    @if ($scheduling['total'] !== null)
                        <p class="text-espresso opacity-70">Tu programa incluye {{ $scheduling['total'] }} {{ $scheduling['total'] === 1 ? 'sesión en vivo' : 'sesiones en vivo' }} con Fabiola</p>
                        @if ($scheduling['used'] > 0)
                            <p class="text-espresso opacity-70">Has agendado {{ $scheduling['used'] }} de {{ $scheduling['total'] }}</p>
                        @endif
                    @endif
                </div>

                @if ($scheduling['remaining'] === 0)
                    <p class="text-espresso opacity-70">Ya agendaste todas las sesiones de tu programa. Si necesitas cambiar una, usa el link del correo de confirmación de Calendly o escríbenos</p>
                @elseif (blank($calendlySchedulingUrl))
                    <p class="text-espresso opacity-70">El agendamiento no está disponible por ahora. Escríbenos y lo coordinamos</p>
                @else
                    <div id="calendly-embed" style="min-width:320px;height:700px;"></div>
                    <p class="text-sm text-espresso opacity-70">Usa el correo de tu cuenta ({{ auth()->user()->email }}) al agendar, así tu clase queda registrada aquí</p>
                    <div id="calendly-success" role="status" aria-live="polite" class="text-sm font-semibold text-espresso"></div>
                    <div id="calendly-error" role="alert" class="text-sm font-semibold text-red-700"></div>
                @endif

                @if ($scheduling['bookings']->isNotEmpty())
                    <div class="flex flex-col gap-2 border-t border-espresso/10 pt-4">
                        <h3 class="font-bold text-espresso">Tus clases agendadas</h3>
                        <ul class="flex flex-col gap-1 text-sm text-espresso">
                            @foreach ($scheduling['bookings'] as $booking)
                                <li>
                                    <time datetime="{{ $booking->scheduled_at->toIso8601String() }}" class="scheduled-time">{{ $booking->scheduled_at->format('d/m/Y H:i') }} UTC</time>
                                </li>
                            @endforeach
                        </ul>
                        <p class="text-sm text-espresso opacity-70">Para cambiar o cancelar una clase, usa el link del correo de confirmación de Calendly</p>
                    </div>

                    <script>
                        document.querySelectorAll('.scheduled-time').forEach(function (el) {
                            var date = new Date(el.getAttribute('datetime'));
                            el.textContent = date.toLocaleString('es', { dateStyle: 'full', timeStyle: 'short' });
                        });
                    </script>
                @endif
            </div>

            @if ($scheduling['remaining'] !== 0 && filled($calendlySchedulingUrl))
                <script src="https://assets.calendly.com/assets/external/widget.js"></script>
                <script>
                    Calendly.initInlineWidget({
                        url: @js($calendlySchedulingUrl),
                        parentElement: document.getElementById('calendly-embed'),
                        prefill: {
                            name: @js(auth()->user()->name),
                            email: @js(auth()->user()->email),
                        },
                    });

                    window.addEventListener('message', function (event) {
                        if (event.origin !== 'https://calendly.com') {
                            return;
                        }

                        if (event.data.event !== 'calendly.event_scheduled') {
                            return;
                        }

                        var successEl = document.getElementById('calendly-success');
                        var errorEl = document.getElementById('calendly-error');
                        errorEl.textContent = '';

                        fetch(@js(route('bookings.confirm')), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @js(csrf_token()),
                            },
                            body: JSON.stringify({
                                event_uri: event.data.payload.event.uri,
                                invitee_uri: event.data.payload.invitee.uri,
                                course_id: @js($course->id),
                            }),
                        })
                            .then(function (response) {
                                if (! response.ok) {
                                    return response.json().catch(function () {
                                        return {};
                                    }).then(function (data) {
                                        throw new Error(data.message || 'No pudimos confirmar tu reserva. Intenta de nuevo o contáctanos.');
                                    });
                                }

                                successEl.textContent = 'Listo, tu clase quedó agendada';
                                setTimeout(function () {
                                    window.location.reload();
                                }, 3000);
                            })
                            .catch(function (error) {
                                errorEl.textContent = (error.message || 'No pudimos confirmar tu reserva. Intenta de nuevo o contáctanos.') + ' Si ya recibiste el correo de Calendly, tu clase sí quedó agendada: escríbenos para registrarla';
                            });
                    });
                </script>
            @endif
        @endif

        @if ($course->modules->isNotEmpty())
            @php $firstLesson = $course->modules->first()->lessons->first(); @endphp

            <a
                href="{{ route('learning.lesson', [$course, $firstLesson]) }}"
                wire:navigate
                class="w-fit shrink-0 rounded-full bg-terracota px-[26px] py-3 text-[15px] font-semibold whitespace-nowrap text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg"
            >Empezar</a>

            <x-course-outline :course="$course" />
        @endif
    </div>
</x-layouts::app>
