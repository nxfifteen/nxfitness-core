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

    var configWeight, configForcastWeight, weightChart, weightForcastChart, weightTrends = '';

    var timeFormat = 'MM/DD/YYYY HH:mm';

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=weight&period=last92", function (data) {
        var returnDate = data.results.returnDate;
        var reportDate = new Date(returnDate[0], returnDate[1] - 1, returnDate[2]);

        if ($('#weightGraph').length > 0) {
            var graphWeightTitle = $('#graphWeightTitle');
            if (graphWeightTitle.length > 0) {
                graphWeightTitle.html("Weight 3 Months");
            }

            graphWeight($, data, reportDate.getTime());
            trendWeight($, data);
        }

        debug_add_gen_time("weight", data.time);
    });

    $('input[name="confWeightTimeRange"]:radio').change(
        function () {
            updateWeightGraph($);
        }
    );

    function updateWeightGraph($) {
        var val = $('input:radio[name=confWeightTimeRange]:checked').val();
        var $progress = $('#animationWeightProgress');
        $progress.show();

        var WeightTrend = $('#WeightTrend');
        if (WeightTrend.length > 0) {
            WeightTrend.html("Generating Dataset...");
        }

        $.getJSON("../json.php?user=" + fitbitUserId + "&data=weight&period=" + val, function (data) {
            var graphWeightTitle = $('#graphWeightTitle');
            if (graphWeightTitle.length > 0) {
                var graphWeightTimeTitle = $('#nameWeight' + val);
                if (graphWeightTimeTitle.length > 0) {
                    graphWeightTitle.html("Weight " + graphWeightTimeTitle.html());
                } else {
                    graphWeightTitle.html("Weight " + val);
                }
            }

            var returnDate = data.results.returnDate;
            var reportDate = new Date(returnDate[0], returnDate[1] - 1, returnDate[2]);

            /** @namespace data.results.graph_weight */
            configWeight.data.datasets[0].data = data.results.graph_weight;
            /** @namespace data.results.graph_weightAvg */
            configWeight.data.datasets[1].data = data.results.graph_weightAvg;
            /** @namespace data.results.graph_weightTrend */
            configWeight.data.datasets[2].data = data.results.graph_weightTrend;
            /** @namespace data.results.graph_weightGoal */
            configWeight.data.datasets[3].data = data.results.graph_weightGoal;

            var pointRadius = 3;
            if (data.results.graph_weight.length > 29) {
                pointRadius = 1;
            }

            configWeight.data.datasets[0].pointRadius = pointRadius;
            configWeight.data.datasets[1].pointRadius = pointRadius;
            configWeight.data.datasets[2].pointRadius = pointRadius;
            configWeight.data.datasets[3].pointRadius = pointRadius;

            var dataTimeScale = [];
            for (var i = 0; i < data.results.graph_weight.length; i++) {
                dataTimeScale.push(reportDate.getTime() - i * 86400000);
            }
            configWeight.data.labels = dataTimeScale;
            weightChart.update();

            var updateDataSet6 = [];
            var dataTimeScaleForcast = [];
            for (i = data.results.graph_weight.length * -1; i < 0; i++) {
                dataTimeScaleForcast.push(reportDate.getTime() - i * 86400000);
                updateDataSet6.push(data.results.graph_weightGoal[0]);
            }
            /** @namespace data.results.graph_weightEst */
            configForcastWeight.data.datasets[0].data = data.results.graph_weightEst;
            configForcastWeight.data.datasets[1].data = updateDataSet6;

            configForcastWeight.data.datasets[0].pointRadius = pointRadius;
            configForcastWeight.data.datasets[1].pointRadius = pointRadius;
            configForcastWeight.data.labels = dataTimeScaleForcast;
            weightForcastChart.update();

            trendWeight($, data);

            debug_add_gen_time("weight " + val, data.time);
        });
    }

    function graphWeight($, data, st) {
        var i, dataSet1, dataSet2, dataSet3, dataSet4, dataSet5, dataSet6, dataTimeScale, aniDuration, pointRadius,
            weight_units;

        /** @namespace data.results.weight_units */
        //noinspection JSValidateTypes
        if (data.results.weight_units === "kg") {
            weight_units = "Kilograms";
        } else { //noinspection JSValidateTypes
            if (data.results.weight_units === "lb") {
                weight_units = "Pounds";
            } else {
                weight_units = data.results.weight_units;
            }
        }

        if (data.results.graph_weight.length > 29) {
            pointRadius = 0;
        } else {
            pointRadius = 1;
        }

        var weightGraph = $('#weightGraph');
        if (weightGraph.length > 0) {
            var $progress = $('#animationWeightProgress');

            $progress.show();

            aniDuration = 3000;

            dataSet1 = data.results.graph_weight;
            dataSet2 = data.results.graph_weightTrend;
            dataSet3 = data.results.graph_weightAvg;
            dataSet4 = data.results.graph_weightGoal;

            dataTimeScale = [];
            for (i = 0; i < dataSet1.length; i++) {
                dataTimeScale.push(st - i * 86400000);
            }

            //noinspection JSUnusedLocalSymbols
            configWeight = {
                type: 'line',
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Body Weight Chart'
                    },
                    tooltips: {
                        mode: 'label',
                        callbacks: {
                            beforeBody: function () {
                                return 'Weights in ' + weight_units;
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
                                labelString: weight_units
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
                        pointBackgroundColor: "#1FB5AD",
                        pointRadius: pointRadius
                    }, {
                        label: "Moving Average",
                        data: dataSet3,
                        borderDash: [5, 5],
                        fill: false,
                        borderColor: "#ac193d",
                        backgroundColor: "#ac193d",
                        pointBackgroundColor: "#ac193d",
                        pointRadius: pointRadius
                    }, {
                        label: "Trend",
                        data: dataSet2,
                        borderDash: [5, 5],
                        fill: false,
                        borderColor: "#37B7F3",
                        backgroundColor: "#37B7F3",
                        pointBackgroundColor: "#37B7F3",
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

            var ctx = document.getElementById("weightGraph").getContext("2d");
            weightChart = new Chart(ctx, configWeight);
        }

        var weightForcastGraph = $('#weightForcastGraph');
        if (weightForcastGraph.length > 0) {
            aniDuration = 3000;

            //noinspection JSUnresolvedVariable
            dataSet5 = data.results.graph_weightEst;

            if (dataSet5.length > 29) {
                pointRadius = 0;
            } else {
                pointRadius = 3;
            }

            dataTimeScale = [];
            dataSet6 = [];
            for (i = dataSet1.length * -1; i < 0; i++) {
                dataTimeScale.push(st - i * 86400000);
                dataSet6.push(dataSet4[0]);
            }

            //noinspection JSUnusedLocalSymbols
            configForcastWeight = {
                type: 'line',
                options: {
                    responsive: true,
                    title: {
                        display: true,
                        text: 'Weight Loss Forcast Chart'
                    },
                    tooltips: {
                        mode: 'label',
                        callbacks: {
                            beforeBody: function () {
                                return 'Weights in ' + weight_units;
                            }
                        }
                    },
                    hover: {
                        mode: 'dataset'
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
                                labelString: weight_units
                            }
                        }]
                    }
                },
                data: {
                    labels: dataTimeScale, // Date Objects
                    datasets: [{
                        label: "Forcast Weight Loss",
                        data: dataSet5,
                        fill: false,
                        borderDash: [5, 5],
                        borderColor: "#1FB5AD",
                        backgroundColor: "#1FB5AD",
                        pointBackgroundColor: "#1FB5AD",
                        pointRadius: pointRadius
                    }, {
                        label: "Target Weight",
                        data: dataSet6,
                        fill: false,
                        borderColor: "#ac193d",
                        backgroundColor: "#ac193d",
                        pointBorderColor: "#ac193d",
                        pointBackgroundColor: "#ac193d",
                        pointRadius: pointRadius
                    }]
                }
            };

            var ctxForcast = document.getElementById("weightForcastGraph").getContext("2d");
            weightForcastChart = new Chart(ctxForcast, configForcastWeight);
        }
    }

    function trendWeight($, data) {
        var WeightTrend = $('#WeightTrend');

        if (WeightTrend.length > 0) {
            var classTable = "", htmlString = "";

            /** @namespace data.results.loss_rate_weight */
            if (data.results.loss_rate_weight.length !== 0) {
                htmlString += "<p>The table bellow shows your week on week weight changes</p>";
                htmlString += "<table class=\"table\">";
                htmlString += "<tr><th>Date</th><th>Weekly Trend</th></tr>";

                var perviousWeight = -1;
                for (var key in data.results.WeighInArray) {
                    /** @namespace data.results.WeighInArray */
                    //noinspection JSUnfilteredForInLoop
                    if (perviousWeight <= -1) {
                        classTable = ' class=""';
                    } else if (data.results.WeighInArray[key] <= perviousWeight) {
                        classTable = ' class="badge-success"';
                    } else {
                        classTable = ' class="badge-danger"';
                    }
                    perviousWeight = data.results.WeighInArray[key];
                    //noinspection JSUnfilteredForInLoop
                    htmlString += "<tr" + classTable + "><td>" + key + "</td><td>" + data.results.WeighInArray[key] + " " + data.results.weight_units + "</td></tr>";
                }

                /*for (var key in data.results.loss_rate_weight) {
                 //noinspection JSUnfilteredForInLoop
                 if (data.results.loss_rate_weight[key] <= 0) {
                 classTable = ' class="success"';
                 } else {
                 //noinspection JSUnfilteredForInLoop
                 if (data.results.loss_rate_weight[key] >= 0.1) {classTable = ' class="danger"';} else {classTable = "";}
                 }
                 //noinspection JSUnfilteredForInLoop
                 htmlString += "<tr"+classTable+"><td>" + key + "</td><td>" + data.results.loss_rate_weight[key] + " " + data.results.weight_units + " </td></tr>";
                 }*/

                htmlString += "</table>";
            }

            if (weightTrends === '') {
                $.getJSON("../json.php?user=" + fitbitUserId + "&data=trend", function (trendData) {
                    /** @namespace trendData.results.weightGoal */
                    /** @namespace trendData.results.caldef */
                    /** @namespace trendData.results.estimatedDate */
                    /** @namespace trendData.results.estimatedWeeks */
                    /** @namespace trendData.results.weightToLose */
                    weightTrends = '<p>Your last recorded weight was <strong>' + trendData.results.weight + '</strong>, and still have another' +
                        ' <strong>' + trendData.results.weightToLose + '</strong> to lose to reach your ' +
                        ' <strong>' + trendData.results.weightGoal + '</strong> goal.</p>' +
                        '<p>Based on your current <strong>' + trendData.results.caldef + '</strong> calorie deficit you should reach your target' +
                        ' weight around <strong>' + trendData.results.estimatedDate + '</strong>, or in about <strong>' + trendData.results.estimatedWeeks +
                        ' weeks</strong>.</p>';

                    htmlString = weightTrends + htmlString;
                    WeightTrend.html(htmlString);

                    debug_add_gen_time("trend", data.time);
                });
            } else {
                htmlString = weightTrends + htmlString;
                WeightTrend.html(htmlString);
            }

        }

    }

});