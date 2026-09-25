<x-layouts::app :title="'Reservar clase'">
    <div class="flex h-full w-full flex-1 flex-col gap-4">
        <div id="calendly-embed" style="min-width:320px;height:700px;"></div>
    </div>

    <script>
        (function () {
            function initCalendly() {
                Calendly.initInlineWidget({
                    url: @js(config('services.calendly.scheduling_url')),
                    parentElement: document.getElementById('calendly-embed'),
                });
            }

            if (window.Calendly) {
                initCalendly();
            } else {
                var widgetScript = document.createElement('script');
                widgetScript.src = 'https://assets.calendly.com/assets/external/widget.js';
                widgetScript.onload = initCalendly;
                document.head.appendChild(widgetScript);
            }
        })();

        window.addEventListener('message', function (event) {
            if (event.origin !== 'https://calendly.com') {
                return;
            }

            if (event.data.event !== 'calendly.event_scheduled') {
                return;
            }

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
                }),
            });
        });
    </script>
</x-layouts::app>
