$(function () {
    'use strict';

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=NomieDashboard", function (data) {

        $('#trackerCount').html(data.results.trackers);
        $('#eventCount').html(data.results.events);

        $('#positiveEvents').html(data.results.spread.events.positive);
        $('#negativeEvents').html(data.results.spread.events.negative);
        $('#neutralEvents').html(data.results.spread.events.netural);

        var splitPieChart = $('#splitPieChart');
        if (splitPieChart.length > 0) {
            var totalPositive = data.results.spread.events.positive;
            var totalNegative = data.results.spread.events.negative;
            var totalNetural = data.results.spread.events.netural;
            var totalIntake = totalPositive + totalNegative + totalNetural;

            var splitPieChartData = [];
            splitPieChartData[0] = {
                label: "Positive",
                data: (totalPositive / totalIntake) * 100,
                color: '#4dbd74'
            };
            splitPieChartData[1] = {
                label: "Negative",
                data: (totalNegative / totalIntake) * 100,
                color: '#f86c6b'
            };
            splitPieChartData[2] = {
                label: "Netural",
                data: (totalNetural / totalIntake) * 100,
                color: '#63c2de'
            };

            $.plot(splitPieChart, splitPieChartData, {
                series: {
                    pie: {
                        show: true,
                        radius: 1,
                        tilt: 0.5,
                        label: {
                            show: true,
                            radius: 1,
                            formatter: labelFormatter,
                            background: {
                                opacity: 0.8
                            }
                        }
                    }
                },
                legend: {
                    show: false
                }
            });
        }

    });

    function labelFormatter(label, series) {
        return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }
});