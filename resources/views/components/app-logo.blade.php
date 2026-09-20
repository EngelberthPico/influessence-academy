@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand {{ $attributes }}>
        <x-slot name="logo" class="h-8 w-auto rounded-none">
            <img src="{{ asset('images/brand/logo-espresso.png') }}" alt="Influessence" class="h-8 w-auto">
            <svg class="ms-2 size-3.5 shrink-0 text-terracota" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 0C12.6 6.7 17.3 11.4 24 12C17.3 12.6 12.6 17.3 12 24C11.4 17.3 6.7 12.6 0 12C6.7 11.4 11.4 6.7 12 0Z" />
            </svg>
            <span class="ms-2 font-serif text-xl text-espresso italic">Academy</span>
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand {{ $attributes }}>
        <x-slot name="logo" class="h-8 w-auto rounded-none">
            <img src="{{ asset('images/brand/logo-espresso.png') }}" alt="Influessence" class="h-8 w-auto">
            <svg class="ms-2 size-3.5 shrink-0 text-terracota" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 0C12.6 6.7 17.3 11.4 24 12C17.3 12.6 12.6 17.3 12 24C11.4 17.3 6.7 12.6 0 12C6.7 11.4 11.4 6.7 12 0Z" />
            </svg>
            <span class="ms-2 font-serif text-xl text-espresso italic">Academy</span>
        </x-slot>
    </flux:brand>
@endif
