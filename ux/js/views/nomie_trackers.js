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
            html += '            <a class="font-weight-bold font-xs btn-block text-muted" <a href="javascript:;" onclick="display_map(document.getElementById(\'gpx\'), \'' + trackerId + '\')">View More <i class="fa fa-angle-right float-right font-lg"></i></a>';
            html += '        </div>';
            html += '    </div>';
            html += '</div>';
        });

        trackers.html(html);

        debug_add_gen_time("nomie", data.time);
    });

});

var cities = L.layerGroup();
var map;

//noinspection JSUnusedGlobalSymbols
function display_map(elt, trackerId) {
    if (!elt) {
        console.log("Z::0");
        return 0;
    }

    if (typeof map !== 'undefined') {
        map.removeLayer(cities);
    }

    var gpxDiv = $('#gpx');
    gpxDiv.show();

    var mapID = elt.getAttribute('data-map-target');
    if (!mapID) {
        console.log("Z::2");
        return 2;
    }

    var mapContainer = $("#gpx-map-container");
    mapContainer.html('<div class="map" id="gpx-map"></div>');

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=NomieGPS&tracker=" + trackerId, function (data) {
        var cities = new L.LayerGroup();
        var locations = data.results.events;

        $.each(locations, function (itemId, mapPoint) {
            // create the marker
            L.marker([mapPoint.geo_lat, mapPoint.geo_lon]).addTo(cities);
        });

        map = L.map(mapID, {
            layers: [cities]
        });


        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Track data from <a href="http://www.fitbit.com">Fitbit</a> and Map data &copy; <a href="http://www.osm.org">OpenStreetMap</a>'
        }).addTo(map);

        map.setView([data.results.lat, data.results.long], '7');
    });
}