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

    function loadWeather($, location, woeid) {
        var weatherDiv = $('#weatherPanel');
        $.simpleWeather({
            location: location,
            woeid: woeid,
            unit: 'c',
            success: function (weather) {
                weatherDiv.find('.wdt-head').html('Conditions in ' + weather.city + ' at ' + weather.updated);
                $('#frcstTodayDay').html("Today - " + weather.currently);
                $('#frcstTodayIco').removeClass("ico-weather").addClass(weatherCodeToIcon(weather.code));
                $('#frcstToday').html(weather.temp);

                setUpWeatherDay($, "One", weather.forecast[1]);
                setUpWeatherDay($, "Two", weather.forecast[2]);
                setUpWeatherDay($, "Three", weather.forecast[3]);
                setUpWeatherDay($, "Four", weather.forecast[4]);
                setUpWeatherDay($, "Five", weather.forecast[5]);
                setUpWeatherDay($, "Six", weather.forecast[6]);
            },
            error: function (error) {
                weatherDiv.find('.panel-body').html('<p>' + error + '</p>');
            }
        });
    }

    function setUpWeatherDay($, dayNumber, forecast) {
        $('#frcst' + dayNumber).html(forecast.high);
        $('#frcst' + dayNumber + 'Ico').removeClass("ico-weather").addClass(weatherCodeToIcon(forecast.code));
        $('#frcst' + dayNumber + 'Day').html(forecast.day);
    }

    function weatherCodeToIcon(weatherCode) {
        //https://developer.yahoo.com/weather/documentation.html#codes
        if (weatherCode === "0") {
            return "ico-windy5";
        } else if (weatherCode === "1") {
            return "ico-lightning5";
        } else if (weatherCode === "2") {
            return "ico-windy5";
        } else if (weatherCode === "3") {
            return "ico-lightning5";
        } else if (weatherCode === "4") {
            return "ico-lightning3";
        } else if (weatherCode === "5") {
            return "ico-snowy2";
        } else if (weatherCode === "6") {
            return "ico-rainy4";
        } else if (weatherCode === "7") {
            return "ico-snowy2";
        } else if (weatherCode === "8") {
            return "ico-snowy";
        } else if (weatherCode === "9") {
            return "ico-rainy";
        } else if (weatherCode === "10") {
            return "ico-snowy3";
        } else if (weatherCode === "11") {
            return "ico-rainy2";
        } else if (weatherCode === "12") {
            return "ico-rainy2";
        } else if (weatherCode === "13") {
            return "ico-snowy2";
        } else if (weatherCode === "14") {
            return "ico-snowy3";
        } else if (weatherCode === "15") {
            return "ico-snowy5";
        } else if (weatherCode === "16") {
            return "ico-snowflake";
        } else if (weatherCode === "17") {
            return "ico-snowy4";
        } else if (weatherCode === "18") {
            return "ico-snowy";
        } else if (weatherCode === "19") {
            return "ico-weather3";
        } else if (weatherCode === "20") {
            return "ico-weather3";
        } else if (weatherCode === "21") {
            return "ico-weather3";
        } else if (weatherCode === "22") {
            return "ico-lines";
        } else if (weatherCode === "23") {
            return "ico-wind";
        } else if (weatherCode === "24") {
            return "ico-windy";
        } else if (weatherCode === "25") {
            return "ico-snowflake";
        } else if (weatherCode === "26") {
            return "ico-cloud2";
        } else if (weatherCode === "27") {
            return "ico-cloud4";
        } else if (weatherCode === "28") {
            return "ico-cloudy3";
        } else if (weatherCode === "29") {
            return "ico-cloud";
        } else if (weatherCode === "30") {
            return "ico-cloudy";
        } else if (weatherCode === "31") {
            return "ico-moon";
        } else if (weatherCode === "32") {
            return "ico-sun";
        } else if (weatherCode === "33") {
            return "ico-moon2";
        } else if (weatherCode === "34") {
            return "ico-sun2";
        } else if (weatherCode === "35") {
            return "ico-rainy4";
        } else if (weatherCode === "36") {
            return "ico-sun3";
        } else if (weatherCode === "37") {
            return "ico-lightning";
        } else if (weatherCode === "38") {
            return "ico-lightning2";
        } else if (weatherCode === "39") {
            return "ico-lightning2";
        } else if (weatherCode === "40") {
            return "ico-rainy2";
        } else if (weatherCode === "41") {
            return "ico-snowy5";
        } else if (weatherCode === "42") {
            return "ico-snowy";
        } else if (weatherCode === "43") {
            return "ico-snowy5";
        } else if (weatherCode === "44") {
            return "ico-cloud2";
        } else if (weatherCode === "45") {
            return "ico-lightning3";
        } else if (weatherCode === "46") {
            return "ico-snowy2";
        } else if (weatherCode === "47") {
            return "ico-lightning2";
        } else {
            return weatherCode;
        }

    }

    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function (position) {
            loadWeather($, position.coords.latitude + ',' + position.coords.longitude, '');

            var weatherPanelImage = $('#weatherPanelImage');
            //noinspection JSUnresolvedVariable
            if (localWeatherImage && weatherPanelImage.length > 0) {
                //noinspection JSUnresolvedVariable
                weatherPanelImage.attr('src', localWeatherImage)
            }
        });
    }

    var StreakGoals = $('#StreakGoals');
    if (StreakGoals.length > 0) {
        $.getJSON("../json.php?user=" + fitbitUserId + "&data=GoalStreak", function (data) {

            var CurrentDays = $('#CurrentDays');
            if (CurrentDays.length > 0) {
                if (data.results.current.days > 0) {
                    CurrentDays.html("Current streak started on " + data.results.current.start + " and has lasted " + data.results.current.days + " days");
                } else {
                    CurrentDays.html("You need to beat your step goal to start a streak");
                }
            }

            var AverageDays = $('#AverageDays');
            if (AverageDays.length > 0) {
                if (parseInt(data.results.current.days) < parseInt(data.results.avg.days)) {
                    AverageDays.html("Lasts " + data.results.avg.days + " days and your " + (data.results.avg.days - data.results.current.days) + " days away from it.");
                } else {
                    AverageDays.html("Is toast! Your betting your " + data.results.avg.days + " day average by " + (data.results.current.days - data.results.avg.days) + " days.");
                }
                $('#AverageDaysProgress').attr('aria-valuenow', data.results.avg.dist).css('width', data.results.avg.dist + '%');
            }

            var LastDays = $('#LastDays');
            if (LastDays.length > 0) {
                if (parseInt(data.results.current.days) < parseInt(data.results.last.days)) {
                    LastDays.html("Ran between " + data.results.last.start + " and " + data.results.last.end + ", lasting " + data.results.last.days + " days and your " + (data.results.last.days - data.results.current.days) + " days away from it.");
                } else {
                    LastDays.html("Is toast! Your betting your last streak of " + data.results.last.days + " day by " + (data.results.current.days - data.results.last.days) + " days.");
                }
                $('#LastDaysProgress').attr('aria-valuenow', data.results.last.dist).css('width', data.results.last.dist + '%');
            }

            var LongestDays = $('#LongestDays');
            if (LongestDays.length > 0) {
                if (parseInt(data.results.current.days) < parseInt(data.results.max.days)) {
                    LongestDays.html("Ran between " + data.results.max.start + " and " + data.results.max.end + ", lasting " + data.results.max.days + " days and your " + (data.results.max.days - data.results.current.days) + " days away from it.");
                } else {
                    LongestDays.html("Is toast! Your betting your longest streak of " + data.results.max.days + " days by " + data.results.current.days + " days.");
                }
                $('#LongestDaysProgress').attr('aria-valuenow', data.results.max.dist).css('width', data.results.max.dist + '%');
            }

            debug_add_gen_time("goal streak", data.time);
        });
    }

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=dashboard&date=" + returnDateString(new Date()), function (data) {
        /** @namespace data.results.syncd */
        if (data.results.syncd === null && typeof data.results.syncd === "object") {
            $('#gaugeStepsPanel').remove();
            $('#gaugeFloorsPanel').remove();
            $('#gaugeDistancePanel').remove();
        } else {
            var gaugeStepsPanel = $('#gaugeStepsPanel');
            if (gaugeStepsPanel.length > 0) {
                /** @namespace data.results.progsteps */
                $('#gaugeStepsText').html(data.results.steps + ' Steps, ' + data.results.progsteps + '%');
                $('#gaugeStepsProgress').attr('aria-valuenow', data.results.progsteps).css('width', data.results.progsteps + '%');
            }

            var gaugeFloorsPanel = $('#gaugeFloorsPanel');
            if (gaugeFloorsPanel.length > 0) {
                /** @namespace data.results.floors */
                /** @namespace data.results.progfloors */
                $('#gaugeFloorsText').html(data.results.floors + ' Floors, ' + data.results.progfloors + '%');
                $('#gaugeFloorsProgress').attr('aria-valuenow', data.results.progfloors).css('width', data.results.progfloors + '%');
            }

            var gaugeDistancePanel = $('#gaugeDistancePanel');
            if (gaugeDistancePanel.length > 0) {
                /** @namespace data.results.progdistance */
                $('#gaugeDistanceText').html(data.results.distance + ' Miles, ' + data.results.progdistance + '%');
                $('#gaugeDistanceProgress').attr('aria-valuenow', data.results.progdistance).css('width', data.results.progdistance + '%');
            }
        }

        debug_add_gen_time("dashboard", data.time);
    });

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=xp", function (data) {
        var xpRow = $('#xpRow');
        if (xpRow.length > 0) {
            /** @namespace data.results.xp */
            $('#xp').html(data.results.xp);
            /** @namespace data.results.level */
            $('#xpLvl').html(data.results.level);
            $('#xpLevelBadge').attr('src', 'img/xplevels/' + data.results.level + '.png');
            $('#xpProgressNextLevel').attr('aria-valuenow', data.results.percent).css('width', data.results.percent + '%');
        }

        debug_add_gen_time("xp", data.time);
    });

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=PendingRewards", function (data) {
        var html = '';
        $.each(data.results.pending, function (index, reward) {
            html += '<li>';
            html += '    <i class="icon-hourglass bg-primary"></i>';
            html += '    <div class="desc">';
            html += '        <div class="title">' + reward.action + '</div>';
            html += '        <small>' + reward.reward + '</small>';
            html += '    </div>';
            html += '    <div class="value">';
            html += '        <div class="small text-muted">' + reward.date + '</div>';
            html += '        <strong>' + reward.state + '</strong>';
            html += '    </div>';
            html += '    <div class="actions">';
            html += '        <button type="button" class="btn btn-link text-muted"><i class="icon-settings"></i>';
            html += '        </button>';
            html += '    </div>';
            html += "</li>\n";
        });
        $.each(data.results.delivered, function (index, reward) {
            html += '<li>';
            html += '    <i class="icon-diamond bg-success"></i>';
            html += '    <div class="desc">';
            html += '        <div class="title">' + reward.action + '</div>';
            html += '        <small>' + reward.reward + '</small>';
            html += '    </div>';
            html += '    <div class="value">';
            html += '        <div class="small text-muted">' + reward.date + '</div>';
            html += '        <strong>' + reward.state + '</strong>';
            html += '    </div>';
            html += '    <div class="actions">';
            html += '        <button type="button" class="btn btn-link text-muted"><i class="icon-settings"></i>';
            html += '        </button>';
            html += '    </div>';
            html += "</li>\n";
        });
        $('#rewardsList').html(html);

        debug_add_gen_time("PendingRewards", data.time);
    });
});
