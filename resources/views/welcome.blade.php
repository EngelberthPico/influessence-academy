<x-layouts.marketing :transparent-nav="true">
        {{-- HERO --}}
        <section class="relative isolate flex min-h-dvh flex-col justify-end overflow-hidden bg-crema-suave px-[6vw] pb-28 lg:flex lg:min-h-screen lg:flex-row lg:items-center lg:justify-normal lg:px-0 lg:pb-0">
            {{-- Foto de fondo: en mobile ocupa toda la sección detrás del texto, con un scrim
                 oscuro (mismo criterio que las tarjetas de la sección CATEGORIAS más abajo) para
                 que el texto se lea encima. En lg: vuelve a ser la columna derecha absoluta de
                 siempre, sin scrim, exactamente como antes --}}
            <div class="absolute inset-0 z-0 lg:left-auto lg:z-auto lg:w-[54%]">
                <img
                    src="{{ asset('images/hero/fabiola-escritorio-editorial.webp') }}"
                    alt="Fabiola, fundadora de Influessence Academy, en su escritorio con un ejemplar impreso con la portada 'Influessence Academy'"
                    width="1536"
                    height="1024"
                    fetchpriority="high"
                    class="hero-photo-mask h-full w-full object-cover object-[65%_32%] lg:object-[76%_center]"
                >
                <div class="absolute inset-0 bg-gradient-to-t from-espresso-oscuro via-espresso-oscuro/45 to-transparent lg:hidden"></div>

                <div class="hidden lg:contents">
                    {{-- Crecimiento en redes --}}
                    <div class="absolute left-6 top-24 w-40 -rotate-6 rounded-sm bg-crema-suave p-4 shadow-lg">
                        <flux:icon.arrow-up-right class="size-5 text-espresso" />
                        <p class="mt-2 text-xs font-bold tracking-wide text-espresso uppercase">Crecimiento en redes</p>
                    </div>

                    {{-- Creación de contenido --}}
                    <div class="absolute right-8 top-36 w-40 rotate-6 rounded-sm bg-crema-suave p-4 shadow-lg">
                        <flux:icon.video-camera class="size-5 text-espresso" />
                        <p class="mt-2 text-xs font-bold tracking-wide text-espresso uppercase">Creación de contenido</p>
                    </div>

                    {{-- Monetización --}}
                    <div class="absolute top-1/2 right-10 w-36 -translate-y-1/2 -rotate-3 rounded-sm bg-crema-suave p-4 shadow-lg">
                        <flux:icon.currency-dollar class="size-5 text-espresso" />
                        <p class="mt-2 text-xs font-bold tracking-wide text-espresso uppercase">Monetización</p>
                    </div>

                    {{-- Marca personal --}}
                    <div class="absolute bottom-28 left-10 w-36 rotate-3 rounded-sm bg-espresso p-4 text-crema shadow-lg">
                        <flux:icon.user class="size-5" />
                        <p class="mt-2 text-xs font-bold tracking-wide uppercase">Marca personal</p>
                    </div>
                </div>
            </div>

            <div class="relative z-10 max-w-[560px] lg:pl-[6vw]">
                <h1 class="mb-[26px] text-[clamp(34px,5.2vw,60px)] leading-[1.12] font-bold tracking-[-0.01em] text-crema lg:text-espresso">
                    Convierte tu contenido en <span class="block font-serif font-normal italic whitespace-nowrap">tu profesión</span>
                </h1>
                <p class="mb-9 max-w-[480px] text-lg leading-[1.65] text-crema opacity-95 lg:text-espresso lg:opacity-[0.85]">
                    Construye una presencia digital auténtica y conviértela en oportunidades. Aprende a crear, conectar con tu comunidad y monetizar tu contenido con un plan que sí puedes sostener.
                </p>
                <a href="{{ route('courses.index') }}" wire:navigate class="inline-block rounded-full bg-terracota px-9 py-4 text-base font-semibold text-crema transition-all duration-300 hover:scale-105 hover:shadow-lg lg:bg-espresso lg:hover:bg-espresso-oscuro">Explorar cursos</a>
            </div>
        </section>

        {{-- MARCAS --}}
        <section class="overflow-hidden bg-crema-suave py-10">
            <p class="mb-6 flex items-center justify-center gap-3 text-center text-sm font-medium text-espresso opacity-70">
                <x-diamond />
                Marcas con las que hemos colaborado
                <x-diamond />
            </p>
            <div class="marquee-mask">
                <div class="marquee-track">
                    @php
                        $marcas = [
                            ['file' => 'la-girl.png', 'alt' => 'LA Girl'],
                            ['file' => 'morphe.svg', 'alt' => 'Morphe'],
                            ['file' => 'garnier.png', 'alt' => 'Garnier'],
                            ['file' => 'loreal.svg', 'alt' => "L'Oréal Paris"],
                            ['file' => 'la-roche-posay.png', 'alt' => 'La Roche-Posay'],
                            ['file' => 'yves-saint-laurent.png', 'alt' => 'Yves Saint Laurent'],
                        ];
                    @endphp
                    <div class="flex shrink-0 items-center gap-16">
                        @foreach ($marcas as $marca)
                            <img src="{{ asset("images/brand/{$marca['file']}") }}" alt="{{ $marca['alt'] }}"
                                 class="{{ $marca['file'] === 'morphe.svg' ? 'h-6 lg:h-7' : 'h-8 lg:h-10' }} w-auto shrink-0">
                        @endforeach
                    </div>
                    {{-- Copia duplicada para el loop continuo — oculta a lectores de pantalla para no repetir la lista --}}
                    <div class="flex shrink-0 items-center gap-16" aria-hidden="true">
                        @foreach ($marcas as $marca)
                            <img src="{{ asset("images/brand/{$marca['file']}") }}" alt=""
                                 class="{{ $marca['file'] === 'morphe.svg' ? 'h-6 lg:h-7' : 'h-8 lg:h-10' }} w-auto shrink-0">
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        {{-- ECOSISTEMA --}}
        <section class="px-[6vw] py-16 lg:py-[100px]">
            <h2 class="mb-[18px] max-w-[620px] text-[clamp(28px,3.5vw,40px)] leading-[1.05] font-bold">Un ecosistema completo <span class="block font-serif font-normal italic">para tu crecimiento</span></h2>
            <p class="mb-12 max-w-[560px] text-[17px] leading-[1.6] opacity-[0.85]">Desde el primer paso hasta la monetización, tienes todo lo que necesitas para convertirte en una creadora que convierte sus ideas en oportunidades reales</p>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(260px,1fr))] gap-7">
                <x-mobile-carousel label="Ecosistema Influessence">
                    <div class="reveal flex h-full flex-col overflow-hidden rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                        <div class="aspect-[16/10] overflow-hidden">
                            <img
                                src="{{ asset('images/ecosistema/academia-influessence-clase-en-vivo.webp') }}"
                                alt="Clase en vivo de Influessence Academy vista desde una laptop, con la cuadrícula de participantes conectadas"
                                width="1200"
                                height="960"
                                loading="lazy"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <div class="flex grow flex-col gap-2.5 bg-crema-suave p-[26px]">
                            <flux:icon.academic-cap class="size-5 text-terracota" />
                            <h3 class="text-[19px] font-bold">Academia Influessence</h3>
                            <p class="grow text-sm leading-[1.55] opacity-[0.85]">Aprende la estrategia completa para crecer, crear y monetizar en redes sociales</p>
                            <a href="{{ route('courses.index') }}" wire:navigate class="mt-1 text-sm font-semibold text-terracota">Ver más</a>
                        </div>
                    </div>

                    <div class="reveal flex h-full flex-col overflow-hidden rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                        <div class="aspect-[16/10] overflow-hidden">
                            <img
                                src="{{ asset('images/ecosistema/ugc-content-creation-productos-belleza.webp') }}"
                                alt="Creadora rodeada de productos de belleza mientras graba contenido UGC"
                                width="1200"
                                height="960"
                                loading="lazy"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <div class="flex grow flex-col gap-2.5 bg-crema-suave p-[26px]">
                            <flux:icon.video-camera class="size-5 text-terracota" />
                            <h3 class="text-[19px] font-bold">UGC &amp; Content Creation</h3>
                            <p class="grow text-sm leading-[1.55] opacity-[0.85]">Domina la creación de contenido real y atractivo para marcas y tu propia audiencia</p>
                            <a href="{{ route('courses.index') }}" wire:navigate class="mt-1 text-sm font-semibold text-terracota">Ver más</a>
                        </div>
                    </div>

                    <div class="reveal flex h-full flex-col overflow-hidden rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                        <div class="aspect-[16/10] overflow-hidden">
                            <img
                                src="{{ asset('images/ecosistema/creator-content-house-experience-batas.webp') }}"
                                alt="Creadoras en batas blancas durante el evento presencial The Creator Content House Experience"
                                width="1200"
                                height="960"
                                loading="lazy"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <div class="flex grow flex-col gap-2.5 bg-crema-suave p-[26px]">
                            <flux:icon.user-group class="size-5 text-terracota" />
                            <h3 class="text-[19px] font-bold">The Creator Content House Experience</h3>
                            <p class="grow text-sm leading-[1.55] opacity-[0.85]">Un evento presencial de Fabiola con creadoras que quieren optimizar su tiempo al crear contenido: retos y estaciones donde en cada una grabas algo distinto en un solo día. Al terminar, sales con hasta 20 videos listos</p>
                            <a href="{{ route('courses.index') }}" wire:navigate class="mt-1 text-sm font-semibold text-terracota">Ver más</a>
                        </div>
                    </div>

                    {{-- Texto pendiente de confirmar con Eli y Fabiola --}}
                    <div class="reveal flex h-full flex-col overflow-hidden rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                        <div class="flex aspect-[16/10] items-center justify-center bg-espresso-oscuro p-5">
                            <p class="m-0 text-center font-mono text-xs text-crema opacity-[0.9]">[ ESPACIO PARA FOTO REAL — escritorio con plantillas impresas y una tablet mostrando una guía ]</p>
                        </div>
                        <div class="flex grow flex-col gap-2.5 bg-crema-suave p-[26px]">
                            <flux:icon.document-text class="size-5 text-terracota" />
                            <h3 class="text-[19px] font-bold">Plantillas &amp; Herramientas</h3>
                            <p class="grow text-sm leading-[1.55] opacity-[0.85]">Plantillas, guías y recursos listos para que implementes todo lo aprendido</p>
                            <a href="{{ route('courses.index') }}" wire:navigate class="mt-1 text-sm font-semibold text-terracota">Ver más</a>
                        </div>
                    </div>
                </x-mobile-carousel>
            </div>

    
        </section>

        {{-- CATEGORIAS --}}
        <section id="categorias" class="scroll-mt-24 bg-crema-suave px-[6vw] py-14 lg:py-[90px]">
            <h2 class="mb-12 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">¿Qué quieres aprender?</h2>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                {{-- Estrategia digital (grande, izquierda) --}}
                <div class="reveal rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                    <a href="{{ route('courses.index') }}" wire:navigate class="category-card group relative isolate flex min-h-[320px] items-end overflow-hidden rounded-sm bg-espresso p-8 lg:min-h-[360px]">
                        <img src="{{ asset('images/categorias/estrategia-digital.webp') }}"
                             alt="Escritorio con laptop, libros de estrategia, una libreta con un plan de contenido y una taza de café en luz natural cálida"
                             class="absolute inset-0 h-full w-full object-cover"
                             loading="lazy">
                        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-espresso-oscuro via-espresso-oscuro/40 to-transparent"></div>
                        <div class="relative z-10 text-crema">
                            <p class="mb-1 text-xs font-semibold tracking-[0.12em] text-terracota uppercase">Estrategia digital</p>
                            <h3 class="mb-3 text-2xl font-bold lg:text-[28px]">Estrategia digital</h3>
                            <p class="mb-4 max-w-[280px] text-sm leading-[1.6] opacity-90">Aprende a crear estrategias digitales que convierten ideas en resultados</p>
                            <span class="text-sm font-semibold underline decoration-terracota decoration-2 underline-offset-4">Explorar cursos</span>
                        </div>
                    </a>
                </div>

                <div class="flex flex-col gap-5">
                    {{-- Creación de contenido --}}
                    <div class="reveal rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                        <a href="#cursos" class="category-card group relative isolate flex min-h-[160px] items-end overflow-hidden rounded-sm bg-terracota p-7 lg:min-h-[180px]">
                            <img src="{{ asset('images/categorias/creacion-de-contenido.webp') }}"
                                 alt="Creadora grabando con el celular montado en un trípode, moodboard de referencias y cámara al fondo"
                                 class="absolute inset-0 h-full w-full object-cover object-[center_65%]"
                                 loading="lazy">
                            <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-espresso-oscuro/80 via-espresso-oscuro/20 to-transparent"></div>
                            <div class="relative z-10 text-crema">
                                <p class="mb-1 text-xs font-semibold tracking-[0.12em] uppercase opacity-80">Creación de contenido</p>
                                <h3 class="text-xl font-bold">Creación de contenido</h3>
                            </div>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        {{-- Redes sociales --}}
                        <div class="reveal rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                            <a href="#cursos" class="category-card group relative isolate flex min-h-[140px] items-end overflow-hidden rounded-sm bg-crema p-6 lg:min-h-[160px]">
                                <img src="{{ asset('images/categorias/redes-sociales.webp') }}"
                                     alt="Celular sobre un escritorio mostrando la grilla de un perfil de Instagram"
                                     class="absolute inset-0 h-full w-full object-cover"
                                     loading="lazy">
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-espresso-oscuro/70 via-espresso-oscuro/10 to-transparent"></div>
                                <div class="relative z-10 text-crema">
                                    <p class="mb-1 text-[11px] font-semibold tracking-[0.1em] uppercase opacity-80">Redes sociales</p>
                                    <h3 class="text-lg font-bold">Redes sociales</h3>
                                </div>
                            </a>
                        </div>

                        {{-- Edición de video --}}
                        <div class="reveal rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                            <a href="#cursos" class="category-card group relative isolate flex min-h-[140px] items-end overflow-hidden rounded-sm bg-espresso p-6 lg:min-h-[160px]">
                                <img src="{{ asset('images/categorias/edicion-de-video.webp') }}"
                                     alt="Laptop con software de edición de video, cámara Sony al lado, escritorio de trabajo"
                                     class="absolute inset-0 h-full w-full object-cover"
                                     loading="lazy">
                                <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-espresso-oscuro via-espresso-oscuro/40 to-transparent"></div>
                                <div class="relative z-10 text-crema">
                                    <p class="mb-1 text-[11px] font-semibold tracking-[0.1em] uppercase opacity-80">Edición de video</p>
                                    <h3 class="text-lg font-bold">Edición de video</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Marca personal (ancha, abajo) --}}
            <div class="reveal mt-5 rounded-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                <a href="{{ route('courses.index') }}" wire:navigate class="category-card group relative isolate flex min-h-[200px] items-end overflow-hidden rounded-sm bg-espresso-oscuro p-8 lg:min-h-[220px]">
                    <img src="{{ asset('images/categorias/marca-personal.webp') }}"
                         alt="Retrato editorial de una mujer sentada junto a una ventana con luz natural, moodboard de referencias en la pared"
                         class="absolute inset-0 h-full w-full object-cover"
                         loading="lazy">
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-espresso-oscuro via-espresso-oscuro/60 to-transparent lg:w-2/3"></div>
                    <div class="relative z-10 max-w-[360px] text-crema">
                        <p class="mb-1 text-xs font-semibold tracking-[0.12em] text-terracota uppercase">Marca personal</p>
                        <h3 class="mb-3 text-2xl font-bold">Marca personal</h3>
                        <p class="mb-4 text-sm leading-[1.6] opacity-90">Construye una presencia auténtica y haz que tu trabajo hable por ti</p>
                        <span class="text-sm font-semibold underline decoration-terracota decoration-2 underline-offset-4">Explorar cursos</span>
                    </div>
                </a>
            </div>
        </section>

        {{-- CURSOS --}}
        <section id="cursos" class="scroll-mt-24 px-[6vw] py-16 lg:py-[100px]">
            @if ($bestSellerCourses->isNotEmpty())
                <h2 class="mb-12 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Los cursos más vendidos</h2>

                <div class="mb-12 grid grid-cols-[repeat(auto-fit,minmax(260px,1fr))] gap-6">
                    <x-mobile-carousel :count="$bestSellerCourses->count()" label="Cursos más vendidos">
                        @foreach ($bestSellerCourses as $course)
                            <a href="{{ route('courses.show', $course) }}" wire:navigate class="reveal flex flex-col gap-4 rounded-sm border border-espresso/15 bg-crema-suave p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_20px_35px_-15px_rgba(42,27,16,0.35)]">
                                <span class="w-fit text-xs font-semibold tracking-wide uppercase text-terracota">{{ $course->card_label }}</span>
                                <h3 class="text-xl font-bold">{{ $course->title }}</h3>
                                <p class="grow text-[15px] leading-[1.6] opacity-[0.85]">{{ Str::limit(strip_tags($course->description), 140) }}</p>
                                <div class="flex items-center justify-between border-t border-espresso/10 pt-4">
                                    @if ($course->price_cents > 0)
                                        <span class="text-xl font-bold text-terracota">${{ number_format($course->price_cents / 100, 0) }}</span>
                                    @else
                                        <span class="text-sm font-semibold opacity-60">Precio próximamente</span>
                                    @endif
                                    <span class="text-sm font-semibold underline decoration-terracota decoration-2 underline-offset-4">Comprar</span>
                                </div>
                            </a>
                        @endforeach
                    </x-mobile-carousel>
                </div>
            @else
                <h2 class="mb-5 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Nuestros cursos</h2>
                <p class="mb-8 max-w-[560px] text-base opacity-70">Explora el catálogo completo</p>
            @endif

            <div class="flex justify-center">
                <a href="{{ route('courses.index') }}" wire:navigate class="rounded-full bg-terracota px-[26px] py-3 text-[15px] font-semibold whitespace-nowrap text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg">Ver todos los cursos</a>
            </div>
        </section>

        {{-- COMO FUNCIONA --}}
        <section id="como-funciona" class="scroll-mt-24 bg-crema-suave px-[6vw] py-16 lg:py-[100px]">
            <h2 class="mb-14 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Cómo funciona la <span class="font-serif font-normal italic">inscripción</span></h2>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-10">
                <div class="reveal">
                    <div class="mb-3 text-[44px] font-bold text-terracota">01</div>
                    <h3 class="mb-2 text-lg font-bold">Elige tu curso</h3>
                    <p class="text-[15px] leading-[1.6] opacity-[0.85]">Según dónde estés hoy: empezando, mejorando tu presencia o monetizando</p>
                </div>
                <div class="reveal">
                    <div class="mb-3 text-[44px] font-bold text-terracota">02</div>
                    <h3 class="mb-2 text-lg font-bold">Acceso inmediato</h3>
                    <p class="text-[15px] leading-[1.6] opacity-[0.85]">Entras a la plataforma y empiezas el mismo día, a tu ritmo</p>
                </div>
                <div class="reveal">
                    <div class="mb-3 text-[44px] font-bold text-terracota">03</div>
                    <h3 class="mb-2 text-lg font-bold">Aprende a tu ritmo</h3>
                    <p class="text-[15px] leading-[1.6] opacity-[0.85]">Los cursos grabados los ves cuando quieras y las veces que necesites, organizados por módulos</p>
                </div>
                <div class="reveal">
                    <div class="mb-3 text-[44px] font-bold text-terracota">04</div>
                    <h3 class="mb-2 text-lg font-bold">Agenda tu clase en vivo</h3>
                    <p class="text-[15px] leading-[1.6] opacity-[0.85]">Si tu curso incluye asesoría, o es un programa en vivo, agendas tu clase con Fabiola desde tu cuenta</p>
                </div>
            </div>
        </section>

        {{-- TESTIMONIO --}}
        <section id="nosotros" class="grid scroll-mt-24 grid-cols-1 items-center gap-14 px-[6vw] py-16 lg:grid-cols-[0.85fr_1.15fr] lg:py-[100px]">
            <div class="reveal aspect-square overflow-hidden rounded-sm">
                <img
                    src="{{ asset('images/nosotros/fabiola-mesa-recortes-prensa.webp') }}"
                    alt="Fabiola, fundadora de Influessence Academy, de pie junto a una mesa con recortes de prensa de la academia"
                    width="1200"
                    height="1800"
                    loading="lazy"
                    class="h-full w-full object-cover object-[50%_45%]"
                >
            </div>
            <div class="reveal">
                <p class="mb-7 font-serif text-[28px] leading-[1.4] text-espresso italic">
                    "Yo también sentí pena de mostrarme. Lo que me cambió no fue perder la pena de un día para otro, fue tener un plan y personas al lado que me lo recordaran cada semana"
                </p>
                <p class="text-base font-semibold">Fabiola</p>
                <p class="mt-1 text-[15px] opacity-70">Fundadora, Influessence Academy</p>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="bg-crema-suave px-[6vw] py-16 lg:py-[100px]">
            <h2 class="mb-12 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Preguntas frecuentes</h2>
            <div class="flex max-w-[760px] flex-col">
                <details class="reveal border-b border-espresso py-[22px]">
                    <summary class="flex cursor-pointer list-none items-center gap-3 text-lg font-semibold [&::-webkit-details-marker]:hidden"><x-diamond />¿Necesito ya tener seguidores para empezar?</summary>
                    <p class="mt-[14px] text-[15px] leading-[1.7] opacity-[0.85]">No. La mayoría de nuestras estudiantes empieza desde cero o con cuentas pequeñas. Nuestros cursos están diseñados para construir desde ahí, con estrategia real en lugar de esperar a que algo se viralice</p>
                </details>
                <details class="reveal border-b border-espresso py-[22px]">
                    <summary class="flex cursor-pointer list-none items-center gap-3 text-lg font-semibold [&::-webkit-details-marker]:hidden"><x-diamond />¿Los cursos son en vivo o grabados?</summary>
                    <p class="mt-[14px] text-[15px] leading-[1.7] opacity-[0.85]">Depende del curso. Los cursos grabados los ves a tu ritmo y no incluyen sesiones en vivo. Los programas en vivo son con Fabiola, y algunos cursos grabados cierran con una asesoría con ella</p>
                </details>
                <details class="reveal border-b border-espresso py-[22px]">
                    <summary class="flex cursor-pointer list-none items-center gap-3 text-lg font-semibold [&::-webkit-details-marker]:hidden"><x-diamond />¿Puedo tomar más de un curso a la vez?</summary>
                    <p class="mt-[14px] text-[15px] leading-[1.7] opacity-[0.85]">Sí. Muchas estudiantes combinan un curso grabado con una asesoría en vivo desde el inicio. Te ayudamos a definir el orden según tu situación</p>
                </details>
                <details class="reveal border-b border-espresso py-[22px]">
                    <summary class="flex cursor-pointer list-none items-center gap-3 text-lg font-semibold [&::-webkit-details-marker]:hidden"><x-diamond />¿Qué pasa si tengo poco tiempo cada semana?</summary>
                    <p class="mt-[14px] text-[15px] leading-[1.7] opacity-[0.85]">El sistema está pensado para mujeres con trabajo, hijos y otras responsabilidades. No se trata de publicar más, sino de publicar con intención en el tiempo que sí tienes</p>
                </details>
            </div>
        </section>

</x-layouts.marketing>
