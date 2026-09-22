<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="box-border min-h-screen bg-crema pt-[var(--nav-height)] text-espresso">
        <x-site-nav :transparent="false" />

        {{ $slot }}

        <x-site-footer />

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
