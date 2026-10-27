<x-layouts::app :title="$course->title">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <a href="{{ route('learning.index') }}" wire:navigate class="w-fit text-sm font-semibold text-espresso underline decoration-terracota decoration-2 underline-offset-4">Volver a mi cuenta</a>

        <h1 class="text-[clamp(28px,3.5vw,40px)] font-bold text-espresso">{{ $course->title }}</h1>

        <div class="prose-none max-w-none text-[17px] leading-[1.7] text-espresso">
            {!! $course->description !!}
        </div>

        @if ($pageUrls->isEmpty())
            <div class="rounded-sm bg-crema-suave p-6">
                <p class="text-espresso opacity-70">Esta guía todavía no tiene páginas disponibles</p>
            </div>
        @else
            <div
                x-data="{
                    current: 0,
                    total: {{ $pageUrls->count() }},
                    urls: @js($pageUrls->values()),
                    title: @js($course->title),
                    retryCount: 0,
                    retryTimer: null,
                    pageFailed: false,
                    go(index) {
                        if (index < 0 || index >= this.total) return;
                        this.current = index;
                        this.resetPageState();
                        this.$nextTick(() => {
                            this.scrollViewerIntoView();
                            this.scrollThumbIntoView();
                        });
                    },
                    next() { this.go(this.current + 1); },
                    prev() { this.go(this.current - 1); },
                    pageSrc() {
                        const url = this.urls[this.current];
                        if (!url) return url;
                        return this.retryCount > 0
                            ? url + (url.includes('?') ? '&' : '?') + 'r=' + this.retryCount
                            : url;
                    },
                    resetPageState() {
                        clearTimeout(this.retryTimer);
                        this.retryTimer = null;
                        this.retryCount = 0;
                        this.pageFailed = false;
                    },
                    handleImageLoad() {
                        this.pageFailed = false;
                        this.preloadNext();
                    },
                    handleImageError() {
                        const delays = [1000, 2500, 5000];

                        if (this.retryCount >= delays.length) {
                            this.pageFailed = true;
                            return;
                        }

                        this.retryTimer = setTimeout(() => {
                            this.retryCount++;
                        }, delays[this.retryCount]);
                    },
                    retryPage() {
                        this.resetPageState();
                        this.retryCount = 1;
                    },
                    touchStartX: null,
                    touchStartY: null,
                    isZoomedIn() {
                        return window.visualViewport && window.visualViewport.scale > 1;
                    },
                    handleTouchStart(event) {
                        if (event.touches.length > 1 || this.isZoomedIn()) {
                            this.touchStartX = null;
                            this.touchStartY = null;
                            return;
                        }
                        this.touchStartX = event.touches[0].clientX;
                        this.touchStartY = event.touches[0].clientY;
                    },
                    handleTouchEnd(event) {
                        if (this.touchStartX === null || this.touchStartY === null) return;

                        const deltaX = event.changedTouches[0].clientX - this.touchStartX;
                        const deltaY = event.changedTouches[0].clientY - this.touchStartY;
                        this.touchStartX = null;
                        this.touchStartY = null;

                        if (this.isZoomedIn()) return;
                        if (Math.abs(deltaX) < 50 || Math.abs(deltaX) < Math.abs(deltaY) * 2) return;

                        if (deltaX < 0) {
                            this.next();
                        } else {
                            this.prev();
                        }
                    },
                    scrollViewerIntoView() {
                        const rect = this.$refs.viewer.getBoundingClientRect();
                        if (rect.top < 0 || rect.top > window.innerHeight * 0.5) {
                            this.$refs.viewer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    },
                    scrollThumbIntoView() {
                        this.$refs.thumbs
                            ?.querySelector('[data-guide-thumb=\'' + this.current + '\']')
                            ?.scrollIntoView({ block: 'nearest', inline: 'nearest' });
                    },
                    preloadNext() {
                        const url = this.urls[this.current + 1];
                        if (url) {
                            (new Image()).src = url;
                        }
                    },
                }"
                @keydown.window.arrow-right="next()"
                @keydown.window.arrow-left="prev()"
                class="select-none"
                oncontextmenu="return false"
            >
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-[6rem_minmax(0,48rem)] lg:items-start lg:justify-center lg:gap-4">
                    @if ($pageUrls->count() > 1)
                        {{-- Controles: fila 1 en móvil, debajo de la página activa en escritorio --}}
                        <div class="row-start-1 flex w-full max-w-3xl items-center justify-between lg:col-start-2 lg:row-start-2">
                            <button
                                type="button"
                                @click="prev()"
                                :disabled="current === 0"
                                class="rounded-full border border-terracota px-6 py-2.5 text-sm font-semibold text-terracota transition-all duration-300 hover:scale-105 hover:bg-terracota hover:text-crema disabled:pointer-events-none disabled:opacity-40"
                            >Anterior</button>

                            <p class="text-sm font-semibold text-espresso" aria-live="polite">Página <span x-text="current + 1"></span> de {{ $pageUrls->count() }}</p>

                            <button
                                type="button"
                                @click="next()"
                                :disabled="current === total - 1"
                                class="rounded-full bg-terracota px-6 py-2.5 text-sm font-semibold text-crema transition-all duration-300 hover:scale-105 hover:bg-espresso hover:shadow-lg disabled:pointer-events-none disabled:opacity-40"
                            >Siguiente</button>
                        </div>
                    @endif

                    {{-- Página activa: fila 2 en móvil, columna derecha en escritorio --}}
                    <div
                        x-ref="viewer"
                        @touchstart.passive="handleTouchStart($event)"
                        @touchend.passive="handleTouchEnd($event)"
                        class="row-start-2 flex min-h-[60vh] flex-col items-center justify-center gap-4 touch-pan-y touch-pinch-zoom lg:col-start-2 lg:row-start-1"
                    >
                        <img
                            x-show="!pageFailed"
                            :src="pageSrc()"
                            :alt="'Página ' + (current + 1) + ' de ' + title"
                            decoding="async"
                            draggable="false"
                            @load="handleImageLoad()"
                            x-on:error="handleImageError()"
                            class="h-auto w-full max-w-3xl rounded-sm border border-espresso/15"
                        >

                        <div x-show="pageFailed" class="flex flex-col items-center gap-4 text-center">
                            <p class="text-sm font-semibold text-espresso opacity-80">No se pudo cargar esta página</p>
                            <button
                                type="button"
                                @click="retryPage()"
                                class="rounded-full border border-terracota px-6 py-2.5 text-sm font-semibold text-terracota transition-all duration-300 hover:scale-105 hover:bg-terracota hover:text-crema"
                            >Reintentar</button>
                        </div>
                    </div>

                    @if ($pageUrls->count() > 1)
                        {{-- Miniaturas: tira horizontal en móvil (fila 3), columna izquierda sticky en escritorio --}}
                        <div
                            x-ref="thumbs"
                            class="row-start-3 flex snap-x snap-mandatory gap-2 overflow-x-auto pb-1 lg:col-start-1 lg:row-start-1 lg:row-span-2 lg:sticky lg:top-24 lg:max-h-[calc(100vh-8rem)] lg:w-24 lg:snap-none lg:flex-col lg:gap-2 lg:overflow-x-visible lg:overflow-y-auto lg:pb-0"
                        >
                            @foreach ($thumbnails as $index => $thumbnail)
                                <button
                                    type="button"
                                    data-guide-thumb="{{ $index }}"
                                    @click="go({{ $index }})"
                                    :aria-current="current === {{ $index }} ? 'true' : 'false'"
                                    aria-label="Ir a la página {{ $index + 1 }}"
                                    class="shrink-0 snap-start rounded-sm border border-espresso/15 p-0.5 transition-all duration-300"
                                    :class="current === {{ $index }} ? 'ring-2 ring-terracota' : ''"
                                >
                                    @if ($thumbnail)
                                        <img
                                            src="{{ $thumbnail }}"
                                            alt="Página {{ $index + 1 }} de {{ $course->title }}"
                                            draggable="false"
                                            class="h-auto w-16 rounded-sm lg:w-full"
                                        >
                                    @else
                                        <div class="flex h-16 w-16 items-center justify-center rounded-sm bg-crema-suave text-xs font-semibold text-espresso/60 lg:aspect-square lg:w-full" aria-hidden="true">{{ $index + 1 }}</div>
                                    @endif
                                    <span class="block text-center text-[11px] text-espresso opacity-70">{{ $index + 1 }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>
