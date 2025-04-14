<x-filament::widget>
    <x-filament::card>
        <div wire:ignore.self>
            <div id="map" style="width: 100%; height: 500px;"></div>
        </div>
    </x-filament::card>
</x-filament::widget>

@once
    @push('scripts')
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap">
        </script>

        <script>
            let mapInitialized = false;
            
            function initMap() {
                const map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: 19.4326, lng: -99.1332 }, // CDMX por defecto
                    zoom: 10,
                });

                // Aquí puedes agregar marcadores si tienes coordenadas
                // new google.maps.Marker({ position: { lat: XX, lng: YY }, map });
            }
        </script>
    @endpush
@endonce
