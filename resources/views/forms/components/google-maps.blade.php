<div>
    <div id="map" style="height: 400px; width: 100%;"></div>
    <input type="hidden" {{ $attributes->merge($getExtraAttributes()) }} x-data="{ value: @entangle($attributes->wire('model')) }" x-model="value">
</div>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD5GrMJChF-2dtYYqMYPL_87V3ngjj5qTU&callback=initMap&libraries=places&v=beta"
    async
    defer>
</script>

<script>
    function initMap() {
        const initialPosition = { lat: 19.432608, lng: -99.133209 }; // Ciudad de México por defecto

        const map = new google.maps.Map(document.getElementById('map'), {
            center: initialPosition,
            zoom: 14,
        });

        const marker = new google.maps.Marker({
            position: initialPosition,
            map: map,
            draggable: true,
            title: 'Arrastra para seleccionar la ubicación',
        });

        // Actualizar el valor en el formulario cuando se mueva el marcador
        marker.addListener('dragend', (event) => {
            const coordinates = `${event.latLng.lat()},${event.latLng.lng()}`;
            @this.set('{{ $attributes->wire('model') }}', coordinates);
        });

        // Actualizar el marcador al hacer clic en el mapa
        map.addListener('click', (event) => {
            marker.setPosition(event.latLng);
            const coordinates = `${event.latLng.lat()},${event.latLng.lng()}`;
            @this.set('{{ $attributes->wire('model') }}', coordinates);
        });
    }

    window.initMap = initMap;
</script>
