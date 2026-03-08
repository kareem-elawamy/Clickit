"use strict";

async function googleMapInitialize() {
    let latitude = parseFloat($("#get-default-latitude").data('latitude')) || -33.8688;
    let longitude = parseFloat($("#get-default-longitude").data('longitude')) || 151.2195;
    let myLatLng = { lat: latitude, lng: longitude };

    const { Map } = await google.maps.importLibrary("maps");
    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    // Initialize Map
    const map = new Map(document.getElementById("location-map-canvas"), {
        center: myLatLng,
        zoom: 13,
        mapId: "roadmap",
    });

    // Store markers in an array to clear them later
    let markers = [];

    // Add initial marker
    let marker = new AdvancedMarkerElement({
        position: myLatLng,
        map: map,
    });
    markers.push(marker);  // Save the initial marker

    let geocoder = new google.maps.Geocoder();

    // Click Event: Reset all markers and add new one
    google.maps.event.addListener(map, 'click', function (event) {
        let coordinates = event.latLng.toJSON();
        let latlng = new google.maps.LatLng(coordinates.lat, coordinates.lng);

        // Remove all existing markers
        markers.forEach(marker => {
            marker.setMap(null);  // Remove each marker
        });
        markers = []; // Clear the markers array

        // Add a new marker at the clicked location
        marker = new AdvancedMarkerElement({
            position: latlng,
            map: map,
        });

        // Save the new marker
        markers.push(marker);

        map.panTo(latlng); // Move map center to the clicked location

        // Update latitude and longitude inputs
        try {
            document.getElementById('latitude').value = coordinates.lat;
            document.getElementById('longitude').value = coordinates.lng;
            $("#get-default-latitude").html(coordinates.lat);
            $("#get-default-longitude").html(coordinates.lng);
        } catch (e) {}

        // Geocode to get the address from the clicked coordinates
        geocoder.geocode({ 'latLng': latlng }, function (results, status) {
            if (status === google.maps.GeocoderStatus.OK && results[1]) {
                document.getElementById('shop-address').value = results[1].formatted_address;
            }
        });
    });

    // Search Box Logic
    const input = document.getElementById("map-pac-input");
    if (input) {
        const searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

        map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
        });

        // Search result markers
        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();
            if (places.length === 0) return;

            // Remove all markers when a new search is made
            markers.forEach(marker => {
                marker.setMap(null);  // Remove each marker
            });
            markers = []; // Clear the markers array

            const bounds = new google.maps.LatLngBounds();

            places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) return;

                // Create a new marker for each place
                let newMarker = new AdvancedMarkerElement({
                    map,
                    title: place.name,
                    position: place.geometry.location,
                });

                google.maps.event.addListener(newMarker, "click", function () {
                    // When a search result marker is clicked, update the coordinates
                    document.getElementById('latitude').value = this.position.lat();
                    document.getElementById('longitude').value = this.position.lng();
                    $("#get-default-latitude").html(this.position.lat());
                    $("#get-default-longitude").html(this.position.lng());
                });

                markers.push(newMarker);  // Add new marker to the markers array

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });

            // Fit the map bounds to the markers
            map.fitBounds(bounds);
        });
    }
}

$(document).on('ready', function () {
    try {
        googleMapInitialize();
    } catch (e) {
    }
});
