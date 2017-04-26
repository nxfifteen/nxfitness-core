$(function () {
    'use strict';

    var configFat, fatChart, fatTrends = '';

    var timeFormat = 'MM/DD/YYYY HH:mm';

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=weight&period=last92", function (data) {
        var returnDate = data.results.returnDate;
        var reportDate = new Date(returnDate[0], returnDate[1] - 1, returnDate[2]);

        if ($('#fatGraph').length > 0) {
            var graphFatTitle = $('#graphFatTitle');
            if (graphFatTitle.length > 0) {
                graphFatTitle.html("Body Fat 3 Months");
            }

            graphFat($, data, reportDate.getTime());
            trendFat($, data);
        }

        debug_add_gen_time("fat", data.time);
    });

    $('input[name="confFatTimeRange"]:radio').change(
        function () {
            updateFatGraph($);
        }
    );

    function updateFatGraph($) {
        var val = $('input:radio[name=confFatTimeRange]:checked').val();
        var $progressFat = $('#animationFatProgress');
        $progressFat.show();

        var FatTrend = $('#FatTrend');
        if (FatTrend.length > 0) {
            FatTrend.html("Generating Dataset...");
        }

        $.getJSON("../json.php?user=" + fitbitUserId + "&data=weight&period=" + val, function (data) {
            var graphFatTitle = $('#graphFatTitle');
            if (graphFatTitle.length > 0) {
                var graphFatTimeTitle = $('#nameFat' + val);
                if (graphFatTimeTitle.length > 0) {
                    graphFatTitle.html("Body Fat " + graphFatTimeTitle.html());
                } else {
                    graphFatTitle.html("Body Fat " + val);
                }
            }

            var returnDate = data.results.returnDate;
            var reportDate = new Date(returnDate[0], returnDate[1] - 1, returnDate[2]);

            configFat.data.datasets[0].data = data.results.graph_fat;
            configFat.data.datasets[1].data = data.results.graph_fatAvg;
            configFat.data.datasets[2].data = data.results.graph_fatTrend;
            configFat.data.datasets[3].data = data.results.graph_fatGoal;

            var pointRadius = 3;
            if (data.results.graph_fat.length > 29) {
                pointRadius = 0;
            }

            configFat.data.datasets[0].pointRadius = pointRadius;
            configFat.data.datasets[1].pointRadius = pointRadius;
            configFat.data.datasets[2].pointRadius = pointRadius;
            configFat.data.datasets[3].pointRadius = pointRadius;

            var dataTimeScale = [];
            for (var i = 0; i < data.results.graph_fat.length; i++) {
                dataTimeScale.push(reportDate.getTime() - i * 86400000);
            }
            configFat.data.labels = dataTimeScale;

            fatChart.update();
            trendFat($, data);

            debug_add_gen_time("fat " + val, data.time);
        });
    }

    function graphFat($, data, st) {
        var i, dataSet1, dataSet2, dataSet3, dataSet4, dataTimeScale, aniDuration, pointRadius;

        var fatGraph = $('#fatGraph');
        if (fatGraph.length > 0) {
            var $progressFat = $('#animationFatProgress');
            $progressFat.show();

            aniDuration = 3000;

            dataSet1 = data.results.graph_fat;
            dataSet2 = data.results.graph_fatTrend;
            dataSet3 = data.results.graph_fatAvg;
            dataSet4 = data.results.graph_fatGoal;

            if (dataSet1.length > 29) {
                pointRadius = 0;
            } else {
                pointRadius = 3;
            }

            dataTimeScale = [];
            for (i = 0; i < dataSet1.length; i++) {
                dataTimeScale.push(st - i * 86400000);
            }

            configFat = {
                type: 'line',
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Body Fat Chart'
                    },
                    tooltips: {
                        mode: 'label',
                        callbacks: {
                            beforeBody: function () {
                                return '% Body Fat';
                            }
                        }
                    },
                    hover: {
                        mode: 'dataset'
                    },
                    animation: {
                        duration: aniDuration,
                        onProgress: function (animation) {
                            $progressFat.attr({
                                value: animation.animationObject.currentStep / animation.animationObject.numSteps,
                            });
                        },
                        onComplete: function (animation) {
                            window.setTimeout(function () {
                                $progressFat.hide();
                            }, (aniDuration * 0.5));
                        }
                    },
                    scales: {
                        xAxes: [{
                            type: "time",
                            time: {
                                format: timeFormat,
                                round: 'day',
                                tooltipFormat: 'll'
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Date'
                            }
                        }],
                        yAxes: [{
                            scaleLabel: {
                                display: true,
                                labelString: '%'
                            }
                        }]
                    }
                },
                data: {
                    labels: dataTimeScale, // Date Objects
                    datasets: [{
                        label: "Recorded Value",
                        data: dataSet1,
                        fill: false,
                        borderColor: "#1FB5AD",
                        backgroundColor: "#1FB5AD",
                        pointRadius: pointRadius
                    }, {
                        label: "Moving Average",
                        data: dataSet3,
                        borderDash: [5, 5],
                        fill: false,
                        borderColor: "#ac193d",
                        backgroundColor: "#ac193d",
                        pointRadius: pointRadius
                    }, {
                        label: "Trend",
                        data: dataSet2,
                        borderDash: [5, 5],
                        fill: false,
                        borderColor: "#37B7F3",
                        backgroundColor: "#37B7F3",
                        pointRadius: pointRadius
                    }, {
                        label: "Target Weight",
                        hidden: true,
                        data: dataSet4,
                        fill: false,
                        display: false,
                        borderColor: "#ac193d",
                        backgroundColor: "#ac193d",
                        pointBorderColor: "#ac193d",
                        pointBackgroundColor: "#ac193d",
                        pointRadius: pointRadius
                    }]
                }
            };

            var ctx = document.getElementById("fatGraph").getContext("2d");
            fatChart = new Chart(ctx, configFat);
        }
    }

    function trendFat($, data, st) {
        var FatTrend = $('#FatTrend');
        if (FatTrend.length > 0) {
            var classTable = "", htmlString = "";

            if (data.results.loss_rate_fat.length != 0) {
                htmlString += "<p>The table bellow shows your month on month body fat changes</p>";
                htmlString += "<table class=\"table table-striped\">";
                htmlString += "<tr><th>Date</th><th>Monthly Trend</th></tr>";

                for (var key in data.results.loss_rate_fat) {
                    if (data.results.loss_rate_fat[key] <= 0) {
                        classTable = ' class="success"';
                    } else if (data.results.loss_rate_fat[key] >= 0.1) {
                        classTable = ' class="danger"';
                    } else {
                        classTable = "";
                    }
                    htmlString += "<tr" + classTable + "><td>" + key + "</td><td>" + data.results.loss_rate_fat[key] + "%</td></tr>";
                }

                htmlString += "</table>";
            }

            if (fatTrends == '') {
                $.getJSON("../json.php?user=" + fitbitUserId + "&data=trend", function (trendData) {
                    fatTrends = '<p>You last recorded <strong>' + trendData.results.fat + '%</strong> body fat, so still has another' +
                        ' <strong>' + trendData.results.fatToLose + '%</strong> still to lose to reach your <strong>' + trendData.results.fatGoal + '%</strong> goal.</p>';

                    htmlString = fatTrends + htmlString;
                    FatTrend.html(htmlString);
                });
            } else {
                htmlString = fatTrends + htmlString;
                FatTrend.html(htmlString);
            }
        }
    }
});