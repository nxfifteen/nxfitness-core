$(function () {
    'use strict';

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=NomieDashboard", function (data) {

        /** @namespace data.results.trackers */
        $('#trackerCount').html(data.results.trackers);
        $('#eventCount').html(data.results.events);

        /** @namespace data.results.spread.events.positive */
        /** @namespace data.results.spread */
        $('#positiveEvents').html(data.results.spread.events.positive);
        $('#negativeEvents').html(data.results.spread.events.negative);
        /** @namespace data.results.spread.events.netural */
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

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=NomieScoreGraph&period=last92", function (data) {
        var dataSet2, dataSet3, dataSet4, aniDuration;

        var scoreGraph = $('#scoreGraph');
        if (scoreGraph.length > 0) {
            var $progress = $('#animationScoreProgress');


            $progress.show();

            aniDuration = 3000;

            dataSet2 = data.results.graph.positive;
            /** @namespace data.results.graph */
            dataSet3 = data.results.graph.negative;
            /** @namespace data.results.graph.neutral */
            dataSet4 = data.results.graph.neutral;

            var configWeight = {
                labels: data.results.graph.dates,
                datasets: [
                    {
                        label: 'Positive',
                        backgroundColor: 'rgba(185,215,195,0.2)',
                        borderColor: '#4dbd74',
                        pointBackgroundColor: '#4dbd74',
                        pointBorderColor: '#fff',
                        data: dataSet2
                    },
                    {
                        label: 'Negative',
                        backgroundColor: 'rgba(220,190,190,0.2)',
                        borderColor: '#f86c6b',
                        pointBackgroundColor: '#f86c6b',
                        pointBorderColor: '#fff',
                        data: dataSet3
                    },
                    {
                        label: 'Netural',
                        backgroundColor: 'rgba(210,228,240,0.2)',
                        borderColor: '#63c2de',
                        pointBackgroundColor: '#63c2de',
                        pointBorderColor: '#fff',
                        data: dataSet4
                    }
                ]
            };

            var ctx = document.getElementById("scoreGraph");
            //noinspection JSUnusedLocalSymbols,JSUnusedLocalSymbols
            var weightChart = new Chart(ctx, {
                type: 'line',
                data: configWeight,
                options: {
                    responsive: true,
                    tooltips: {
                        mode: 'label',
                        callbacks: {
                            beforeBody: function () {
                                return 'Events Recorded';
                            }
                        }
                    },
                    hover: {
                        mode: 'dataset'
                    },
                    animation: {
                        duration: aniDuration,
                        onProgress: function (animation) {
                            $progress.attr({
                                value: animation.animationObject.currentStep / animation.animationObject.numSteps
                            });
                        },
                        onComplete: function (animation) {
                            window.setTimeout(function () {
                                $progress.hide();
                            }, (aniDuration * 0.5));
                        }
                    }
                }
            });
        }
    });

    function labelFormatter(label, series) {
        return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }
});