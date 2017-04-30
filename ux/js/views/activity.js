$(function () {
    'use strict';

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=TrackerHistoryChart&period=last7", function (data) {
        /** @namespace data.results.stepsGoal */
        /** @namespace data.results.human */
        /** @namespace data.results.precentages */
        /** @namespace data.results.analysis */
        /** @namespace data.results.analysis.steps7Day */
        /** @namespace data.results.analysis.floors7Day */
        /** @namespace data.results.analysis.distance7Day */
        /** @namespace data.results.analysis.stepsYesterday */
        /** @namespace data.results.analysis.stepsYesterdayRaw */
        /** @namespace data.results.analysis.floorsYesterday */
        /** @namespace data.results.analysis.distanceYesterday */
        var barChartData = {
            labels: data.results.date,
            datasets: [
                {
                    label: 'Steps Goal',
                    type: 'line',
                    fill: false,
                    display: false,
                    backgroundColor: "#ac193d",
                    borderColor: "#ac193d",
                    highlightFill: "#ac193d",
                    highlightStroke: "#ac193d",
                    data: data.results.stepsGoal
                },
                {
                    label: 'Steps',
                    backgroundColor: "#b1e340",
                    data: data.results.steps
                }
            ],
            options: {
                elements: {
                    rectangle: {
                        borderWidth: 2,
                        borderColor: '#b1e340',
                        borderSkipped: 'bottom'
                    }
                },
                responsive: true,
                legend: {
                    position: 'top'
                },
                title: {
                    display: false
                }
            }
        };
        var ctx = document.getElementById('canvas-2');
        //noinspection JSUnusedLocalSymbols
        var chart = new Chart(ctx, {
            type: 'bar',
            data: barChartData,
            options: {
                responsive: true
            }
        });

        var weeklyTarget = $('#weeklyStepsGoal');
        if (weeklyTarget.length > 0) {
            $('#weeklyStepsTaken').html(data.results.human.steps + " Steps");
            weeklyTarget.html(data.results.human.stepsGoal + " Steps");
        }

        var stepsProgressbar = $('#stepsProgressbar');
        if (stepsProgressbar.length > 0) {
            stepsProgressbar.attr('aria-valuenow', data.results.precentages.steps).css('width', data.results.precentages.steps + '%');
        }

        var steps7Day = $('#steps7Day');
        if (steps7Day.length > 0) {
            steps7Day.html(data.results.analysis.steps7Day + " Steps");
        }

        var floors7Day = $('#floors7Day');
        if (floors7Day.length > 0) {
            floors7Day.html(data.results.analysis.floors7Day + " Floors");
        }

        var distance7Day = $('#distance7Day');
        if (distance7Day.length > 0) {
            distance7Day.html(data.results.analysis.distance7Day + " Miles");
        }

        var stepsYesterday = $('#stepsYesterday');
        if (stepsYesterday.length > 0) {
            if (data.results.analysis.stepsYesterdayRaw < 0) {
                stepsYesterday.css({"color": "#197910"});
                stepsYesterday.html("<strong>" + data.results.analysis.stepsYesterday + " Steps</strong> over");
            } else {
                stepsYesterday.html("<strong>" + data.results.analysis.stepsYesterday + " Steps</strong> to go");
            }
        }

        var floorsYesterday = $('#floorsYesterday');
        if (floorsYesterday.length > 0) {
            if (data.results.analysis.stepsYesterdayRaw < 0) {
                floorsYesterday.css({"color": "#197910"});
                floorsYesterday.html("<strong>" + data.results.analysis.floorsYesterday + " Floors</strong> over");
            } else {
                floorsYesterday.html("<strong>" + data.results.analysis.floorsYesterday + " Floors</strong> to go");
            }
        }

        var distanceYesterday = $('#distanceYesterday');
        if (distanceYesterday.length > 0) {
            if (data.results.analysis.stepsYesterdayRaw < 0) {
                distanceYesterday.css({"color": "#197910"});
                distanceYesterday.html("<strong>" + data.results.analysis.distanceYesterday + " Miles</strong> over");
            } else {
                distanceYesterday.html("<strong>" + data.results.analysis.distanceYesterday + " Miles</strong> to go");
            }
        }

        debug_add_gen_time("tracker history", data.time);
    });

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

});