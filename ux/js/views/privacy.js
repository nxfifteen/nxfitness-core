/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

var spotlat = 0;
var spotlon = 0;

$(function () {
    'use strict';


    if (location.protocol === 'https:' && "geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function (position) {
            mapGPS("Your Locaton", position.coords.latitude, position.coords.longitude);
        });
    } else {
        mapGPS("St Andrews", 56.33861769463613, -2.798058986663819);
    }

    getMapPoints($);

    $("#search").click(function () {
        mapQuery();
    });

    function mapQuery() {
        var val = $('#searchAddress').val();
        $.getJSON("http://open.mapquestapi.com/nominatim/v1/search.php?key=PsAr6ZmaaU6y0j5WMhA1SyIDJMtLIAXp&format=json&q=" + val + "&addressdetails=1&limit=3&viewbox=-1.99%2C52.02%2C0.78%2C50.94&exclude_place_ids=41697", function (data) {
            mapGPS(data[0].display_name, data[0].lat, data[0].lon);
        });
    }
});

var map;
var mapContainer = $("#map-container");

function mapGPS(name, lat, lng) {
    var zoomLev;
    if (typeof map !== 'undefined') {
        zoomLev = map.getZoom();
    } else {
        zoomLev = 16;
    }

    spotlat = lat;
    spotlon = lng;

    $('#header').html(name);

    var mapID = document.getElementById('gpx').getAttribute('data-map-target');
    mapContainer.html('<div class="map" id="map"></div>');

    map = L.map(mapID, {
        center: [lat, lng],
        zoom: 7,
        maxZoom: 16
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: 'Track data from <a href="https://nomie.io" target="_blank">Nomie</a> and Map data &copy; <a href="http://www.osm.org">OpenStreetMap</a>'
    }).addTo(map);

    L.circle([lat, lng], 200).addTo(map);
    L.marker([lat, lng]).addTo(map);
    map.on('click', onMapClick);

    map.setView([lat, lng], zoomLev);
}

function onMapClick(e) {
    mapGPS("Map Point - " + e.latlng.lat + "," + e.latlng.lng, e.latlng.lat, e.latlng.lng);
}

// this is the id of the form
$("#privacyPoint").submit(function (e) {

    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#privacyPoint").serialize() + "&lat="+spotlat + "&lon="+spotlon;

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $("#display_name").val("");
        getMapPoints($);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

function privacyPointDel(pointName) {
    var url = "../ajax.php"; // the script where you handle the form input.

    var data = "formId=privacyPointDel&point=" + pointName;

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $("#display_name").val("");
        getMapPoints($);
    });
}

function getMapPoints($) {
    $.getJSON("../json.php?user=" + fitbitUserId + "&data=GeoSecure", function (data) {
        var html = '';
        $.each(data.results, function (index, locationPoint) {
            html += '  <div class="col-sm-6 col-lg-3">';
            html += '    <div class="card card-inverse card-primary">';
            /** @namespace locationPoint.display_name */
            html += '      <div class="card-header" id="header"><a style="color: white" href="javascript:;" onclick="mapGPS(\'' + locationPoint.display_name.split('\'').join('') + '\', ' + locationPoint.lat + ', ' + locationPoint.lon + ');">' + locationPoint.display_name + '</a></div>';
            html += '      <div class="card-block" id="location-map-' + index + '">';
            /** @namespace locationPoint.lon */
            html += '        <img class="img-fluid" src="inc/StaticMapLite.php?center=' + locationPoint.lat + ',' + locationPoint.lon + '&zoom=14&size=380x150&maptype=mapnik&markers=' + locationPoint.lat + ',' + locationPoint.lon + ',ol-marker" width="380" height="150" />';
            html += '      </div>';
            html += '      <div class="card-footer">';
            html += '        <button class="btn btn-danger" type="button" onclick="privacyPointDel(\'' + locationPoint.display_name.split('\'').join('\\\'') + '\')">Delete</button>';
            html += '      </div>';
            html += '    </div>';
            html += '  </div>';

            if (locationPoint.display_name === "Home") {
                mapGPS("Your " + locationPoint.display_name, locationPoint.lat, locationPoint.lon);
            }

        });
        $("#locations-map").html(html);
    });
}