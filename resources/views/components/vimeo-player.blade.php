@props(['url', 'title'])

<div {{ $attributes->class('aspect-video w-full overflow-hidden rounded-sm bg-espresso-oscuro shadow-sm') }}>
    {{-- No agregar referrerpolicy aquí ni un header Referrer-Policy en el proyecto: la restricción
         de dominio de Vimeo depende de que el navegador envíe el Referer. Si en el futuro se agrega
         una Content-Security-Policy, debe incluir frame-src https://player.vimeo.com --}}
    <iframe
        src="{{ $url }}"
        title="{{ $title }}"
        class="size-full"
        loading="lazy"
        allow="autoplay; fullscreen; picture-in-picture"
        allowfullscreen
    ></iframe>
</div>
