$(function () {
    'use strict';

    $('#gpx').hide();
    $.getJSON("../json.php?user=" + fitbitUserId + "&data=NomieTrackers", function (data) {
        var html = '';
        var trackers = $('#trackers');
        var dbTrackers = data.results;

        $.each(dbTrackers, function (trackerId, dbTracker) {
            html += '<div class="col-12 col-md-6 col-lg-4" id="' + trackerId + '">';
            html += '    <div class="card">';
            html += '        <div class="card-block p-3 clearfix" style="background-color: ' + dbTracker.color + ' !important; ">';
            html += '            <div class="row">';
            html += '                <div class="hidden-sm-down col-md-3">';
            html += '                    <i class="' + dbTracker.icon + ' p-3 mr-3 float-left" style="color: #ffffff !important; font-size: 50px;line-height: 50px;"></i>';
            html += '                </div>';
            html += '                <div class="col-12 col-md-9">';
            html += '                    <div class="row">';
            html += '                        <div class="col-12 col-md-12">';
            html += '                            <div class="h5 mb-0 mt-2" style="color: #ffffff;">' + dbTracker.label + '</div>';
            html += '                        </div>';
            html += '                    </div>';
            html += '                    <div class="row">';
            html += '                        <div class="col-6 col-md-6">';
            html += '                            <div class="text-muted text-uppercase font-weight-bold font-xs" style="color: #ffffff !important;">' + dbTracker.stats.events + ' Events</div>';
            html += '                        </div>';
            html += '                        <div class="col-6 col-md-6">';

            if (dbTracker.charge < -1) {
                html += '                            <div class="text-muted text-uppercase font-weight-bold font-xs" style="color: #ffffff !important;">' + dbTracker.charge + ' points lost</div>';
            } else if (dbTracker.charge < 0) {
                html += '                            <div class="text-muted text-uppercase font-weight-bold font-xs" style="color: #ffffff !important;">' + dbTracker.charge + ' point lost</div>';
            } else if (dbTracker.charge > 1) {
                html += '                            <div class="text-muted text-uppercase font-weight-bold font-xs" style="color: #ffffff !important;">' + dbTracker.charge + ' points awarded</div>';
            } else if (dbTracker.charge > 0) {
                html += '                            <div class="text-muted text-uppercase font-weight-bold font-xs" style="color: #ffffff !important;">' + dbTracker.charge + ' point awarded</div>';
            }

            html += '                        </div>';
            html += '                    </div>';
            html += '                    <div class="row">';
            html += '                        <div class="col-6 col-md-6">';
            html += '                            <div class="text-muted text-uppercase font-weight-bold font-xs" style="color: #ffffff !important;">Daily Avg : ' + dbTracker.stats.dayAvg + '<br />Monthly Avg : ' + dbTracker.stats.monthAvg + '</div>';
            html += '                        </div>';
            html += '                        <div class="col-6 col-md-6">';
            html += '                            <div class="text-muted text-uppercase font-weight-bold font-xs" style="color: #ffffff !important;">First : ' + dbTracker.stats.first + '<br />Last : ' + dbTracker.stats.last + '</div>';
            html += '                        </div>';
            html += '                    </div>';
            html += '                </div>';
            html += '            </div>';
            html += '        </div>';
            html += '        <div class="card-footer px-3 py-2">';
            html += '            <a class="font-weight-bold font-xs btn-block text-muted" <a href="javascript:;" onclick="display_map(document.getElementById(\'gpx\'), \'' + trackerId + '\', \'' + dbTracker.label + '\', \'' + dbTracker.icon + '\', \'' + dbTracker.color + '\')">View on Map <i class="fa fa-angle-right float-right font-lg"></i></a>';
            html += '        </div>';
            html += '    </div>';
            html += '</div>';
        });

        trackers.html(html);

        debug_add_gen_time("nomie", data.time);
    });

});

var map;
var markers = L.markerClusterGroup();
var gpxDiv = $('#gpx');
var mapContainer = $("#gpx-map-container");
var mapModal = $('#myModal');

//noinspection JSUnusedGlobalSymbols
function display_map(elt, trackerId, label, icon, color) {
    if (!elt) {
        console.log("Z::0");
        return 0;
    }

    if (typeof map !== 'undefined') {
        markers.clearLayers();
    }

    $('#modal-title').html('<i class="' + icon + ' p-3 mr-3 float-left" style="color: #ffffff !important; font-size: 50px;line-height: 50px;"></i> <span style="color: #ffffff !important;"> ' + label + "</span>");
    $('#modal-header').css("background-color", color);

    gpxDiv.show();

    var mapID = elt.getAttribute('data-map-target');
    if (!mapID) {
        console.log("Z::2");
        return 2;
    }

    mapContainer.html('<div class="map" id="gpx-map"></div>');

    var markerList = [];
    $.getJSON("../json.php?user=" + fitbitUserId + "&data=NomieGPS&tracker=" + trackerId, function (data) {

        var locations = data.results.events;

        $.each(locations, function (itemId, mapPoint) {

            var title = "Recorded: " + mapPoint.datestamp;
            var marker = L.marker([mapPoint.geo_lat, mapPoint.geo_lon]).bindPopup(title);
            markerList.push(marker);
        });


        map = L.map(mapID, {
            center: [data.results.lat, data.results.long],
            zoom: 7,
            maxZoom: 16
        });

        markers.addLayers(markerList);
        map.addLayer(markers);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Track data from <a href="https://nomie.io" target="_blank">Nomie</a> and Map data &copy; <a href="http://www.osm.org">OpenStreetMap</a>'
        }).addTo(map);

        map.setView([data.results.lat, data.results.long], '7');

        debug_add_gen_time("Map " + label, data.time);
    });

    mapModal.modal('show');
    mapModal.on("shown.bs.modal", function () {
        map.invalidateSize();
    });
}