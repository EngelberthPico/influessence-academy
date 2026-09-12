<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="overflow-x-hidden bg-crema font-sans text-espresso antialiased">
        {{-- NAV --}}
        <nav class="fixed inset-x-0 top-0 z-50 grid grid-cols-[auto_1fr_auto] items-center gap-4 px-[6vw] py-4 transition-colors duration-300">
            <a href="{{ route('home') }}" wire:navigate>
                <img src="{{ asset('images/brand/logo-espresso.png') }}" alt="Influessence Academy" class="h-8 w-auto">
            </a>
            <div class="hidden items-center justify-center gap-7 lg:flex">
                <a href="#cursos" class="text-[15px] font-medium text-espresso">Cursos</a>
                <a href="#categorias" class="text-[15px] font-medium text-espresso">Categorías</a>
                <a href="#como-funciona" class="text-[15px] font-medium text-espresso">Cómo funciona</a>
                <a href="#nosotras" class="text-[15px] font-medium text-espresso">Nosotras</a>
            </div>
            <div class="flex items-center justify-end gap-6">
                <a href="{{ route('login') }}" class="hidden text-[15px] font-medium text-espresso sm:inline" wire:navigate>Iniciar sesión</a>
                <a href="#cursos" class="shrink-0 rounded-full bg-terracota px-[26px] py-3 text-[15px] font-semibold whitespace-nowrap text-crema">Quiero crecer</a>
            </div>
        </nav>

        {{-- HERO --}}
        <section class="grid grid-cols-1 items-center gap-10 bg-crema-suave px-[6vw] pt-24 pb-16 lg:grid-cols-2 lg:gap-16 lg:pt-28 lg:pb-24">
            <div>
                <h1 class="mb-[26px] leading-[1.12] font-bold tracking-[-0.01em]">
                    <span class="text-[clamp(40px,6.2vw,72px)]">Convierte tu contenido en</span><span class="-mt-2 block font-serif text-[clamp(38px,5.8vw,66px)] leading-none font-normal whitespace-nowrap italic">Tu Profesión</span>
                </h1>
               
                <p class="mb-9 max-w-[480px] text-lg leading-[1.65] text-espresso opacity-[0.85]">
                    Aquí no te enseñamos a copiarle el estilo a nadie. Te enseñamos a crear, conectar con tu comunidad y monetizar tu contenido con un plan que sí puedes sostener.
                </p>
                <a href="#cursos" class="inline-block rounded-full bg-espresso px-9 py-4 text-base font-semibold text-crema">Explorar cursos</a>
            </div>
            <div class="flex justify-center lg:justify-end">
                <img
                    src="{{ asset('images/hero/fabiola-portada-periodico.webp') }}"
                    alt="Fabiola, fundadora de Influessence Academy, sosteniendo un ejemplar impreso con la portada 'Influessence Academy'"
                    width="1400"
                    height="2100"
                    fetchpriority="high"
                    class="aspect-[4/5] w-full max-w-[480px] object-cover shadow-[inset_0_0_90px_28px_var(--color-crema)]"
                >
            </div>
        </section>

        {{-- DIFERENCIACION --}}
        <section class="grid grid-cols-1 items-start gap-14 px-[6vw] py-16 lg:grid-cols-[0.85fr_1.15fr] lg:pt-[110px] lg:pb-[90px]">
            <h2 class="text-[clamp(26px,3.2vw,36px)] leading-[1.18] font-bold">
                Aprendes de personas que viven realmente de esto
            </h2>
            <div class="flex max-w-[580px] flex-col gap-[22px] text-[17px] leading-[1.7] text-espresso opacity-[0.88]">
                <p>En Udemy o Domestika te enseña alguien que grabó un curso una vez. Aquí te enseña Fabiola, que sigue creando contenido todos los días y sabe exactamente qué cambió este mes en el algoritmo, qué funciona para creadoras hispanas y qué ya no.</p>
                <p>No son clases genéricas de marketing digital traducidas al español. Son las mismas estrategias que usamos con nuestras propias cuentas y con las marcas que trabajamos en Influessence Agency, adaptadas a tu etapa y tu ritmo.</p>
            </div>
        </section>

        {{-- ECOSISTEMA --}}
        <section class="px-[6vw] py-16 lg:py-[100px]">
            <h2 class="mb-[18px] max-w-[620px] text-[clamp(28px,3.5vw,40px)] font-bold">Un ecosistema completo para tu crecimiento</h2>
            <p class="mb-12 max-w-[560px] text-[17px] leading-[1.6] opacity-[0.85]">Desde el primer paso hasta la monetización, tienes todo lo que necesitas para convertirte en la creadora que siempre soñaste ser.</p>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(260px,1fr))] gap-7">
                <div class="flex flex-col">
                    <div class="flex aspect-[16/10] items-center justify-center bg-terracota p-5">
                        <p class="m-0 text-center font-mono text-xs text-crema opacity-[0.9]">[ ESPACIO PARA FOTO REAL — Fabiola dando una clase en vivo, laptop y notas al frente ]</p>
                    </div>
                    <div class="flex flex-col gap-2.5 bg-crema-suave p-[26px]">
                        <h3 class="text-[19px] font-bold">Academia Influessence</h3>
                        <p class="text-sm leading-[1.55] opacity-[0.85]">Aprende la estrategia completa para crecer, crear y monetizar en redes sociales.</p>
                        <a href="#cursos" class="mt-1 text-sm font-semibold text-terracota">Ver más</a>
                    </div>
                </div>
                <div class="flex flex-col">
                    <div class="flex aspect-[16/10] items-center justify-center bg-espresso-oscuro p-5">
                        <p class="m-0 text-center font-mono text-xs text-crema opacity-[0.9]">[ ESPACIO PARA FOTO REAL — estudiante grabando UGC con el celular en un espacio cotidiano ]</p>
                    </div>
                    <div class="flex flex-col gap-2.5 bg-crema-suave p-[26px]">
                        <h3 class="text-[19px] font-bold">UGC &amp; Content Creation</h3>
                        <p class="text-sm leading-[1.55] opacity-[0.85]">Domina la creación de contenido real y atractivo para marcas y tu propia audiencia.</p>
                        <a href="#cursos" class="mt-1 text-sm font-semibold text-terracota">Ver más</a>
                    </div>
                </div>
                {{-- Texto pendiente de confirmar con Eli y Fabiola --}}
                <div class="flex flex-col">
                    <div class="flex aspect-[16/10] items-center justify-center bg-terracota p-5">
                        <p class="m-0 text-center font-mono text-xs text-crema opacity-[0.9]">[ ESPACIO PARA FOTO REAL — grupo de creadoras en un encuentro presencial de la comunidad ]</p>
                    </div>
                    <div class="flex flex-col gap-2.5 bg-crema-suave p-[26px]">
                        <h3 class="text-[19px] font-bold">La Casa de Creadoras</h3>
                        <p class="text-sm leading-[1.55] opacity-[0.85]">Una comunidad exclusiva para crecer juntas, compartir experiencias y oportunidades.</p>
                        <a href="#cursos" class="mt-1 text-sm font-semibold text-terracota">Ver más</a>
                    </div>
                </div>
                {{-- Texto pendiente de confirmar con Eli y Fabiola --}}
                <div class="flex flex-col">
                    <div class="flex aspect-[16/10] items-center justify-center bg-espresso-oscuro p-5">
                        <p class="m-0 text-center font-mono text-xs text-crema opacity-[0.9]">[ ESPACIO PARA FOTO REAL — escritorio con plantillas impresas y una tablet mostrando una guía ]</p>
                    </div>
                    <div class="flex flex-col gap-2.5 bg-crema-suave p-[26px]">
                        <h3 class="text-[19px] font-bold">Plantillas &amp; Herramientas</h3>
                        <p class="text-sm leading-[1.55] opacity-[0.85]">Plantillas, guías y recursos listos para que implementes todo lo aprendido.</p>
                        <a href="#cursos" class="mt-1 text-sm font-semibold text-terracota">Ver más</a>
                    </div>
                </div>
            </div>
        </section>

        {{-- CATEGORIAS --}}
        <section id="categorias" class="scroll-mt-24 bg-crema-suave px-[6vw] py-14 lg:py-[90px]">
            <h2 class="mb-12 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">¿Qué quieres aprender?</h2>
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-4 lg:grid-rows-[repeat(2,minmax(140px,auto))]">
                <a href="#cursos" class="col-span-2 row-span-2 flex items-end rounded-sm bg-espresso p-8 text-crema">
                    <span class="text-2xl leading-[1.2] font-bold">Estrategia digital</span>
                </a>
                <a href="#cursos" class="col-span-2 flex items-end rounded-sm bg-terracota p-7 text-crema">
                    <span class="text-xl font-bold">Creación de contenido</span>
                </a>
                <a href="#cursos" class="flex items-end rounded-sm border-[1.5px] border-espresso p-6 text-espresso">
                    <span class="text-lg font-bold">Redes sociales</span>
                </a>
                <a href="#cursos" class="flex items-end rounded-sm bg-crema p-6 text-espresso">
                    <span class="text-lg font-bold">Edición de video</span>
                </a>
                <a href="#cursos" class="col-span-2 flex items-end rounded-sm border-[1.5px] border-espresso p-6 text-espresso">
                    <span class="text-lg font-bold">Marca personal</span>
                </a>
            </div>
        </section>

        {{-- CURSOS --}}
        <section id="cursos" class="scroll-mt-24 px-[6vw] py-16 lg:py-[100px]">
            {{-- TODO: contenido hardcodeado. Conectar a App\Models\Course cuando se defina cómo mapear los niveles de precio (Asesoría 1:1 / Master Pro / Mentorship / Full Content / catálogo grabado) al esquema existente. --}}
            <h2 class="mb-5 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Elige tu nivel de acompañamiento</h2>

            <p class="mb-8 max-w-[560px] text-base opacity-70">Acompañamiento en vivo con Fabi</p>
            <div class="mb-18 grid grid-cols-1 gap-6 lg:grid-cols-[0.85fr_0.85fr_1.3fr]">
                <div class="flex flex-col gap-[14px] bg-crema-suave p-8">
                    <h3 class="text-xl font-bold">Asesoría 1:1</h3>
                    <p class="grow text-[15px] leading-[1.6] opacity-[0.85]">Una sesión personalizada de 2 horas con Fabi sobre lo que más te está trabando ahora, con un plan de acción claro al salir.</p>
                    <div class="text-xl font-bold text-terracota">$349</div>
                </div>
                <div class="flex flex-col gap-[14px] border-[1.5px] border-espresso p-8">
                    <h3 class="text-xl font-bold">Master Pro</h3>
                    <p class="grow text-[15px] leading-[1.6] opacity-[0.85]">3 meses, 6 clases en vivo de 1 hora, personalizadas con Fabi. Sales con tu estrategia de contenido armada y ajustada semana a semana.</p>
                    <div class="text-xl font-bold text-terracota">$999</div>
                </div>
                <div class="flex flex-col gap-4 bg-espresso px-9 py-10 text-crema">
                    <h3 class="text-[26px] font-bold">Mentorship</h3>
                    <p class="grow text-[15px] leading-[1.65] opacity-90">6 meses de acompañamiento cercano con Fabi: 12 clases en vivo de 1 hora, personalizadas para construir tu carrera de creadora de principio a fin.</p>
                    <div class="text-2xl font-bold text-crema">$1,699</div>
                </div>
            </div>

            <p class="mb-8 max-w-[560px] text-base opacity-70">Curso grabado con acompañamiento final</p>
            <div class="mb-18">
                <div class="flex flex-col items-start gap-6 border-t-[3px] border-terracota bg-crema-suave p-9 sm:flex-row sm:items-end sm:justify-between">
                    <div class="max-w-[640px]">
                        <h3 class="mb-3 text-[22px] font-bold">Full Content</h3>
                        <p class="text-[15px] leading-[1.6] opacity-[0.85]">Clases grabadas para armar tu contenido a tu ritmo, más una asesoría final con Fabi para revisar y ajustar tu estrategia. Redimible dentro de 3 meses.</p>
                    </div>
                    <div class="text-[22px] font-bold text-terracota">$549</div>
                </div>
            </div>

            <p class="mb-8 max-w-[560px] text-base opacity-70">Catálogo de cursos grabados</p>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-5">
                <div class="flex flex-col gap-3 border-[1.5px] border-espresso p-[26px]">
                    <h3 class="text-[17px] font-bold">Amazon Links</h3>
                    <p class="grow text-sm leading-[1.55] opacity-[0.85]">Al terminar vas a saber armar tu tienda de Amazon y crear contenido que genera comisiones de forma constante.</p>
                    <div class="text-lg font-bold text-terracota">$XX</div>
                </div>
                <div class="flex flex-col gap-3 border-[1.5px] border-espresso p-[26px]">
                    <h3 class="text-[17px] font-bold">UGC</h3>
                    <p class="grow text-sm leading-[1.55] opacity-[0.85]">Al terminar vas a tener un portafolio de contenido UGC listo para ofrecer a marcas y cobrar por él.</p>
                    <div class="text-lg font-bold text-terracota">$XX</div>
                </div>
                <div class="flex flex-col gap-3 border-[1.5px] border-espresso p-[26px]">
                    <h3 class="text-[17px] font-bold">Edición básica</h3>
                    <p class="grow text-sm leading-[1.55] opacity-[0.85]">Al terminar vas a editar tus propios videos con un flujo simple, sin depender de nadie más.</p>
                    <div class="text-lg font-bold text-terracota">$XX</div>
                </div>
                <div class="flex flex-col gap-3 border-[1.5px] border-espresso p-[26px]">
                    <h3 class="text-[17px] font-bold">Marca personal</h3>
                    <p class="grow text-sm leading-[1.55] opacity-[0.85]">Al terminar vas a tener tu identidad visual y de voz definida, lista para aplicar en todo tu contenido.</p>
                    <div class="text-lg font-bold text-terracota">$XX</div>
                </div>
            </div>
        </section>

        {{-- COMO FUNCIONA --}}
        <section id="como-funciona" class="scroll-mt-24 bg-crema-suave px-[6vw] py-16 lg:py-[100px]">
            <h2 class="mb-14 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Cómo funciona la inscripción</h2>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-10">
                <div>
                    <div class="mb-3 text-[44px] font-bold text-terracota">01</div>
                    <h3 class="mb-2 text-lg font-bold">Elige tu curso</h3>
                    <p class="text-[15px] leading-[1.6] opacity-[0.85]">Según dónde estés hoy: empezando, mejorando tu presencia o monetizando.</p>
                </div>
                <div>
                    <div class="mb-3 text-[44px] font-bold text-terracota">02</div>
                    <h3 class="mb-2 text-lg font-bold">Acceso inmediato</h3>
                    <p class="text-[15px] leading-[1.6] opacity-[0.85]">Entras a la plataforma y empiezas el mismo día, a tu ritmo.</p>
                </div>
                <div>
                    <div class="mb-3 text-[44px] font-bold text-terracota">03</div>
                    <h3 class="mb-2 text-lg font-bold">Comunidad semanal</h3>
                    <p class="text-[15px] leading-[1.6] opacity-[0.85]">Sesiones en vivo con Fabiola y el grupo para resolver dudas reales.</p>
                </div>
                <div>
                    <div class="mb-3 text-[44px] font-bold text-terracota">04</div>
                    <h3 class="mb-2 text-lg font-bold">Seguimiento a tu avance</h3>
                    <p class="text-[15px] leading-[1.6] opacity-[0.85]">Revisamos tu contenido y tu estrategia, no solo te damos videos grabados.</p>
                </div>
            </div>
        </section>

        {{-- TESTIMONIO --}}
        <section id="nosotras" class="grid scroll-mt-24 grid-cols-1 items-center gap-14 px-[6vw] py-16 lg:grid-cols-[0.85fr_1.15fr] lg:py-[100px]">
            <div class="flex aspect-square items-center justify-center rounded-sm bg-espresso-oscuro p-6">
                <p class="m-0 text-center font-mono text-[13px] text-crema opacity-[0.85]">[ ESPACIO PARA FOTO REAL — Fabiola, fundadora ]</p>
            </div>
            <div>
                <p class="mb-7 font-serif text-[28px] leading-[1.4] text-espresso italic">
                    "Yo también sentí pena de mostrarme. Lo que me cambió no fue perder la pena de un día para otro, fue tener un plan y personas al lado que me lo recordaran cada semana."
                </p>
                <p class="text-base font-semibold">Fabiola</p>
                <p class="mt-1 text-[15px] opacity-70">Fundadora, Influessence Academy</p>
            </div>
        </section>

        {{-- FAQ --}}
        <section class="bg-crema-suave px-[6vw] py-16 lg:py-[100px]">
            <h2 class="mb-12 max-w-[600px] text-[clamp(28px,3.5vw,40px)] font-bold">Preguntas frecuentes</h2>
            <div class="flex max-w-[760px] flex-col">
                <details class="border-b border-espresso py-[22px]">
                    <summary class="cursor-pointer list-none text-lg font-semibold [&::-webkit-details-marker]:hidden">¿Necesito ya tener seguidores para empezar?</summary>
                    <p class="mt-[14px] text-[15px] leading-[1.7] opacity-[0.85]">No. La mayoría de nuestras estudiantes empieza desde cero o con cuentas pequeñas. El programa está diseñado para construir desde ahí, con estrategia real en lugar de esperar a que algo se viralice.</p>
                </details>
                <details class="border-b border-espresso py-[22px]">
                    <summary class="cursor-pointer list-none text-lg font-semibold [&::-webkit-details-marker]:hidden">¿Los cursos son en vivo o grabados?</summary>
                    <p class="mt-[14px] text-[15px] leading-[1.7] opacity-[0.85]">Las clases base son grabadas para que avances a tu ritmo, y cada semana hay sesiones en vivo de acompañamiento y comunidad con Fabiola.</p>
                </details>
                <details class="border-b border-espresso py-[22px]">
                    <summary class="cursor-pointer list-none text-lg font-semibold [&::-webkit-details-marker]:hidden">¿Puedo tomar más de un curso a la vez?</summary>
                    <p class="mt-[14px] text-[15px] leading-[1.7] opacity-[0.85]">Sí. Muchas estudiantes combinan Estrategia de Contenido con Cámara, Voz y Presencia desde el inicio. Te ayudamos a definir el orden según tu situación.</p>
                </details>
                <details class="border-b border-espresso py-[22px]">
                    <summary class="cursor-pointer list-none text-lg font-semibold [&::-webkit-details-marker]:hidden">¿Qué pasa si tengo poco tiempo cada semana?</summary>
                    <p class="mt-[14px] text-[15px] leading-[1.7] opacity-[0.85]">El sistema está pensado para mujeres con trabajo, hijos y otras responsabilidades. No se trata de publicar más, sino de publicar con intención en el tiempo que sí tienes.</p>
                </details>
            </div>
        </section>

        {{-- CTA FINAL + FOOTER --}}
        <section id="contacto" class="bg-espresso-oscuro px-[6vw] pt-16 pb-10 text-crema lg:pt-[100px] lg:pb-[60px]">
            <div class="mb-20 max-w-[640px]">
                <h2 class="mb-5 text-[clamp(30px,4vw,46px)] leading-[1.15] font-bold">Tu carrera de creadora empieza con una decisión, no con una excusa más.</h2>
                <a href="#cursos" class="mt-3 inline-block rounded-full bg-terracota px-[34px] py-4 text-base font-semibold text-crema">Explorar cursos</a>
            </div>
            <div class="flex flex-wrap justify-between gap-8 border-t border-crema/20 pt-8">
                <div>
                    <img src="{{ asset('images/brand/logo-crema.png') }}" alt="Influessence Academy" class="mb-2 h-10 w-auto">
                    <p class="text-[14px] opacity-70">Academia online para creadoras hispanas en Estados Unidos.</p>
                </div>
                <div class="flex flex-wrap gap-8">
                    <a href="#cursos" class="text-[14px] text-crema opacity-[0.85]">Cursos</a>
                    <a href="#categorias" class="text-[14px] text-crema opacity-[0.85]">Categorías</a>
                    <a href="#nosotras" class="text-[14px] text-crema opacity-[0.85]">Nosotras</a>
                    <a href="https://influessenceagency.com" target="_blank" rel="noopener" class="text-[14px] font-semibold text-terracota">Influessence Agency, para negocios</a>
                </div>
            </div>
        </section>

        <script>
            (function () {
                const nav = document.querySelector('nav');
                const onScroll = () => {
                    nav.classList.toggle('bg-crema', window.scrollY > 8);
                };

                onScroll();
                window.addEventListener('scroll', onScroll, { passive: true });
            })();
        </script>
    </body>
</html>
