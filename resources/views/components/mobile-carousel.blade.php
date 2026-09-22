@props(['count' => null, 'label' => 'Carrusel'])

@if (! is_null($count) && $count <= 1)
    {{ $slot }}
@else
    {{-- goTo() usa scroll behavior "auto" (no "smooth"): combinar "smooth" con
         scroll-snap hace que algunos navegadores ignoren el scrollTo por
         completo. El swipe con el dedo sigue siendo suave porque eso lo maneja
         scroll-snap-type de forma nativa, no esta llamada. --}}
    <div
        x-data="{
            active: 0,
            cards: [],
            init() {
                this.cards = Array.from(this.$refs.track.children);
                this.$refs.track.addEventListener('scroll', () => {
                    clearTimeout(this.scrollTimeout);
                    this.scrollTimeout = setTimeout(() => this.updateActive(), 80);
                }, { passive: true });
            },
            updateActive() {
                const track = this.$refs.track;
                const trackRect = track.getBoundingClientRect();
                const trackCenter = trackRect.left + trackRect.width / 2;
                let closest = 0;
                let closestDistance = Infinity;
                this.cards.forEach((card, index) => {
                    const cardRect = card.getBoundingClientRect();
                    const cardCenter = cardRect.left + cardRect.width / 2;
                    const distance = Math.abs(cardCenter - trackCenter);
                    if (distance < closestDistance) {
                        closestDistance = distance;
                        closest = index;
                    }
                });
                this.active = closest;
            },
            goTo(index) {
                const card = this.cards[index];
                if (! card) return;
                const track = this.$refs.track;
                const offset = card.getBoundingClientRect().left - track.getBoundingClientRect().left + track.scrollLeft;
                track.scrollTo({ left: offset, behavior: 'auto' });
                this.active = index;
            },
        }"
        class="md:contents"
    >
        <div
            x-ref="track"
            data-carousel-track
            class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-2 [&>*]:w-[78%] [&>*]:shrink-0 [&>*]:snap-start sm:[&>*]:w-[46%] md:contents md:gap-0 md:overflow-visible md:pb-0 md:[&>*]:w-auto md:[&>*]:shrink md:[&>*]:snap-align-none"
        >
            {{ $slot }}
        </div>

        <div
            x-show="cards.length > 1"
            role="group"
            aria-label="{{ $label }}"
            class="mt-5 flex items-center justify-center gap-2 md:hidden"
        >
            <template x-for="(card, index) in cards" :key="index">
                <button
                    type="button"
                    @click="goTo(index)"
                    class="flex size-11 shrink-0 items-center justify-center"
                    :aria-label="'Ir a la tarjeta ' + (index + 1)"
                    :aria-current="active === index ? 'true' : 'false'"
                >
                    <span
                        class="size-2 rounded-full transition-colors duration-300"
                        :class="active === index ? 'bg-terracota' : 'bg-espresso/20'"
                    ></span>
                </button>
            </template>
        </div>
    </div>
@endif
