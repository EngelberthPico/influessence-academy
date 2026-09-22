<x-layouts::app.sidebar :title="$title ?? null">
    <main class="p-6 lg:p-8">
        {{ $slot }}
    </main>
</x-layouts::app.sidebar>
