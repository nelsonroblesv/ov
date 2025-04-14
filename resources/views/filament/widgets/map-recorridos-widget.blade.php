<x-filament::card>
    <div wire:ignore>
        <div id="map" style="width: 100%; height: 500px;"></div>
    </div>

    <script>
          console.log("Cargando mapa...");
        function initMap() {
            const center = { lat: 19.4326, lng: -99.1332 }; // CDMX como punto central

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 5,
                center: center,
            });

            // Marcador de prueba
            const marker = new google.maps.Marker({
                position: center,
                map: map,
                title: "Ciudad de México",
                icon: "https://maps.google.com/mapfiles/ms/icons/red-dot.png"
            });
        }
    </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap">
    </script>
</x-filament::card>
