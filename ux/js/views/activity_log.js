/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

$(function () {
    'use strict';

    //noinspection JSUnresolvedVariable
    $('#userProfileAvatar').attr('src', userProfileAvatar);
    //noinspection JSUnresolvedVariable
    $('#userProfileName').html(userProfileName);

    $('#gpx').hide();
    $('#dayImpact').hide();
    var activityHistory = $('#ActivityHistory');
    if (activityHistory.length > 0) {
        $.getJSON("../json.php?user=" + fitbitUserId + "&data=ActivityHistory&period=last90", function (data) {

            var isOdd = function (x) {
                return x & 1;
            };
            var isEven = function (x) {
                return !isOdd(x);
            };

            var html = '';
            var indexMenu = '';
            var events = 0;
            $.each(data.results, function (index, month) {
                if (month.length > 0) {
                    indexMenu += '<a href="#' + index.split(",").join("").split(" ").join("") + '">' + index + '</a><br />';
                    html += '<article class="timeline-item alt" id="' + index.split(",").join("").split(" ").join("") + '"><div class="text-right"><div class="time-show first"><a href="#" class="btn btn-primary">' + index + '</a></div></div></article>';
                    $.each(month, function (index, event) {
                        /** @namespace event.logId */
                        html += '<article id="evt-' + event.logId + '" class="timeline-item';
                        if (isEven(events)) {
                            html += ' alt';
                        }
                        html += '">';
                        html += '<div class="timeline-desk">';
                        html += '<div class="card">';
                        /** @namespace event.colour */
                        html += '<div class="card-block ' + event.colour + '">';
                        html += '<span class="arrow';
                        if (isEven(events)) {
                            html += '-alt';
                        }
                        html += '"></span>';
                        html += '<span class="timeline-icon ' + event.colour + '"><i class="fa fa-check"></i></span>';
                        html += '<span class="timeline-date">' + event.startTime + '</span>';
                        html += '<h1 class="' + event.colour + '">';
                        //noinspection JSValidateTypes
                        if (event.visibility === "private") {
                            html += '<i class="fa fa-lock"></i> ';
                        }
                        html += '' + event.name + '</h1>';
                        html += '<div class="row">';
                        html += '<div class="col-md-6">';
                        html += '<div class="eventDay">' + event.startTime + '</div>';
                        /** @namespace event.calPerMinute */
                        html += '<div class="eventEffect"><i class="fa fa-bolt"></i> ' + event.calPerMinute + ' kcals/min</div>';
                        html += '</div>';
                        html += '<div class="col-md-6">';
                        if (event.steps !== "0") {
                            html += '<div class="eventEffect"><i class="fa fa-trophy"></i> +' + event.steps + ' of ' + event.stats.steps + ' Steps</div>';
                        }
                        /** @namespace event.stats.caloriesOut */
                        html += '<div class="eventEffect"><i class="fa fa-fire"></i> +' + event.calories + ' of ' + event.stats.caloriesOut + ' Calories </div>';
                        html += '<div class="eventEffect"><i class="fa fa-clock-o"></i> +' + event.duration + ' of ' + event.stats.active + ' Active Minutes</div>';
                        html += '</div>';
                        html += '</div>';

                        html += '<div class="row d-sm-down-none">';
                        html += '<div class="col-md-12">';
                        var json = JSON.stringify(event);
                        json = json.split("\"").join("\\\'");
                        /** @namespace event.gpx */
                        html += '<div class="eventEffect"><i class="fa fa-clock-o"></i> <a href="javascript:;" onclick="display_gpx(document.getElementById(\'gpx\'), \'' + event.gpx + '\', \'' + json + '\');">View on Map <i class="fa fa-map-marker"></i></a>';
                        //noinspection JSValidateTypes
                        if (event.gpx !== "none") {
                            html += ' | <a href="' + event.gpx + '">Download GPX <i class="fa fa-download"></i></a>';
                        }
                        html += '</div></div>';
                        html += '</div>';

                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</article>';
                        events++;
                    });
                }
            });
            activityHistory.html(html);

            var dateMenu = $('#dateMenu');
            if (dateMenu.length > 0) {
                dateMenu.html(indexMenu);
            }

            debug_add_gen_time("activity history", data.time);
        });
    }
});

//noinspection JSUnusedGlobalSymbols
function display_gpx(elt, gpx_source, activityJson) {
    if (!elt) {
        return 0;
    }
    if (!gpx_source) {
        return 1;
    }

    var wrpTimeline = $('#wrpTimeline');
    var wrpMap = $('#wrpMap');

    var url = gpx_source;
    var mapID = elt.getAttribute('data-map-target');
    if (!url || !mapID) {
        return 2;
    }

    var wrpMapWidth = wrpMap.innerWidth();
    var leftMargin = wrpMap.outerWidth(true);

    $('#profilePanel').hide();
    if (gpx_source !== "none") {
        $('#gpx').show();
    } else {
        $('#gpx').hide();
    }
    $('#dayImpact').show();

    wrpMap.css("position", "fixed");
    wrpMap.css("width", wrpMapWidth);
    wrpTimeline.css("marginLeft", leftMargin);

    var mapContainer = $("#gpx-map-container");
    var mapContainerInfo = $("#gpx-map-info");
    mapContainer.show();
    mapContainerInfo.show();

    mapContainer.html('<div class="map" id="gpx-map"></div>');

    // function _t(t) {
    //     return elt.getElementsByTagName(t)[0];
    // }
    //
    // function _c(c) {
    //     return elt.getElementsByClassName(c)[0];
    // }

    if (gpx_source !== "none") {
        mapContainer.show();
        mapContainerInfo.show();
        var map = L.map(mapID);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Track data from <a href="http://www.fitbit.com">Fitbit</a> and Map data &copy; <a href="http://www.osm.org">OpenStreetMap</a>'
        }).addTo(map);

        new L.GPX(url, {
            async: true,
            marker_options: {
                startIconUrl: 'bower_components/leaflet/dist/images/marker-icon.png',
                endIconUrl: 'bower_components/leaflet/dist/images/marker-icon.png',
                shadowUrl: 'bower_components/leaflet/dist/images/marker-shadow.png'

                // startIconUrl: 'img/pin-icon-start.png',
                // endIconUrl: 'img/pin-icon-end.png',
                // shadowUrl: 'img/pin-shadow.png'
            }
        }).on('loaded', function (e) {
            var gpx = e.target;
            map.fitBounds(gpx.getBounds());

            var gpxInfo = $('#gpx-info');
            gpxInfo.find('.distance').html(gpx.get_distance_imp().toFixed(2));
            gpxInfo.find('.duration').html(gpx.get_duration_string(gpx.get_moving_time()));
            gpxInfo.find('.pace').html(gpx.get_duration_string(gpx.get_moving_pace_imp(), true));
            //noinspection SpellCheckingInspection
            gpxInfo.find('.avghr').html(gpx.get_average_hr());
        }).addTo(map);
    } else {
        mapContainer.hide();
        mapContainerInfo.hide();
    }

    activityJson = activityJson.split("'").join("\"");
    activityJson = JSON.parse(activityJson);
    //noinspection JSUnresolvedVariable
    buildDonutActivityLevel(activityJson.activityLevel);

    /*==Easy Pie chart ==*/
    if ($.fn.easyPieChart) {
        var percentageDisplayed;
        var stepsChart = $('.steps-epie');
        buildDonutImpactLevel(stepsChart, "#ff6c60");
        percentageDisplayed = Math.round((parseInt(activityJson.steps.split(",").join("")) / parseInt(activityJson.stats.steps.split(",").join(""))) * 100);
        if (percentageDisplayed > 100) {
            percentageDisplayed = 100;
        }
        stepsChart.data('easyPieChart').update(percentageDisplayed);

        var caloriesChart = $('.calories-epie');
        buildDonutImpactLevel(caloriesChart, "#FCB322");
        //noinspection JSUnresolvedVariable
        percentageDisplayed = Math.round((parseInt(activityJson.calories) / parseInt(activityJson.stats.caloriesOut.split(",").join(""))) * 100);
        if (percentageDisplayed > 100) {
            percentageDisplayed = 100;
        }
        caloriesChart.data('easyPieChart').update(percentageDisplayed);

        var activityChart = $('.activity-epie');
        buildDonutImpactLevel(activityChart, "#a9d86e");
        //noinspection JSUnresolvedVariable
        percentageDisplayed = Math.round((parseInt(activityJson.duration) / parseInt(activityJson.stats.active)) * 100);
        if (percentageDisplayed > 100) {
            percentageDisplayed = 100;
        }
        activityChart.data('easyPieChart').update(percentageDisplayed);
    }

}

function buildDonutImpactLevel(idName, barColour) {
    //noinspection JSUnusedGlobalSymbols
    idName.easyPieChart({
        onStep: function (from, to, percent) {
            $(this.el).find('.impact').text(Math.round(percent));
        },
        barColor: barColour,
        lineWidth: 5,
        size: 130,
        trackColor: "#efefef",
        scaleColor: "#cccccc"
    });

}

function buildDonutActivityLevel(activityLevel) {
    //noinspection JSUnresolvedVariable
    var totalWorkOut = parseInt(activityLevel.sedentary) + parseInt(activityLevel.lightly) + parseInt(activityLevel.fairly) + parseInt(activityLevel.very);
    //noinspection JSUnresolvedVariable
    var sedentary = Math.round((parseInt(activityLevel.sedentary) / totalWorkOut) * 100);
    //noinspection JSUnresolvedVariable
    var lightly = Math.round((parseInt(activityLevel.lightly) / totalWorkOut) * 100);
    //noinspection JSUnresolvedVariable
    var fairly = Math.round((parseInt(activityLevel.fairly) / totalWorkOut) * 100);
    //noinspection JSUnresolvedVariable
    var very = Math.round((parseInt(activityLevel.very) / totalWorkOut) * 100);

    var jsonData = [];
    var colourData = [];
    if (sedentary > 0) {
        jsonData.push({value: sedentary, label: sedentary + '%', formatted: 'Inactivity'});
        colourData.push('#EF6F66');
    }
    if (lightly > 0) {
        jsonData.push({value: lightly, label: lightly + '%', formatted: 'Slightly Active'});
        colourData.push('#fed65a');
    }
    if (fairly > 0) {
        jsonData.push({value: fairly, label: fairly + '%', formatted: 'Fairly Active'});
        colourData.push('#56C9F5');
    }
    if (very > 0) {
        jsonData.push({value: very, label: very + '%', formatted: 'Very Active'});
        colourData.push('#39B6AE');
    }

    $('#activity-donut').html("");

    // Use Morris.Area instead of Morris.Line
    Morris.Donut({
        element: 'activity-donut',
        data: jsonData,
        backgroundColor: '#fff',
        labelColor: '#1fb5ac',
        colors: colourData,
        formatter: function (x, data) {
            return data.formatted;
        }
    });
}