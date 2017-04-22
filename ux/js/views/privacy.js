$(function () {
    'use strict';

    var map;
    var gpxDiv = $('#gpx');
    var mapContainer = $("#gpx-map-container");

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function (position) {
            mapGPS("Your Locaton", position.coords.latitude, position.coords.longitude);
        });
    }

    $('input[name="example-text-input"]:text').change(
        function () {
            mapQuery();
        }
    );

    $("#gpx-search").click(function () {
        mapQuery();
    });

    function mapQuery() {
        var val = $('#example-text-input').val();
        $.getJSON("http://open.mapquestapi.com/nominatim/v1/search.php?key=PsAr6ZmaaU6y0j5WMhA1SyIDJMtLIAXp&format=json&q=" + val + "&addressdetails=1&limit=3&viewbox=-1.99%2C52.02%2C0.78%2C50.94&exclude_place_ids=41697", function (data) {
            mapGPS(data.display_name, data.lat, data.lon);
        });
    }

    function mapGPS(name, lat, lon) {
        $('#gpx-header').html(name);

        var mapID = document.getElementById('gpx').getAttribute('data-map-target');
        mapContainer.html('<div class="map" id="gpx-map"></div>');

        map = L.map(mapID, {
            center: [lat, lon],
            zoom: 7,
            maxZoom: 16
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Track data from <a href="https://nomie.io" target="_blank">Nomie</a> and Map data &copy; <a href="http://www.osm.org">OpenStreetMap</a>'
        }).addTo(map);

        L.circle([lat, lon], 200).addTo(map);
        L.marker([lat, lon]).addTo(map);

        map.setView([lat, lon], '16');
    }

});