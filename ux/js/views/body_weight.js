$(function () {
    'use strict';

    var configWeight, configFat, weightChart, fatChart, weightTrends = '', fatTrends = '';

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

    $( 'input[name="confWeightTimeRange"]:radio' ).change(
        function () {
            updateWeightGraph( $ );
        }
    );

    function updateWeightGraph($) {
        var val = $( 'input:radio[name=confWeightTimeRange]:checked' ).val();
        var $progress = $('#animationWeightProgress');
        $progress.show();

        var WeightTrend = $('#WeightTrend');
        if (WeightTrend.length > 0) {
            WeightTrend.html("Generating Dataset...");
        }

        $.getJSON( "../json.php?user=" + fitbitUserId + "&data=weight&period=" + val, function (data) {
            var graphWeightTitle = $( '#graphWeightTitle' );
            if ( graphWeightTitle.length > 0 ) {
                var graphWeightTimeTitle = $( '#nameWeight' + val );
                if ( graphWeightTimeTitle.length > 0 ) {
                    graphWeightTitle.html( "Weight " + graphWeightTimeTitle.html() );
                } else {
                    graphWeightTitle.html( "Weight " + val );
                }
            }

            var returnDate = data.results.returnDate;
            var reportDate = new Date( returnDate[0], returnDate[1] - 1, returnDate[2] );

            configWeight.data.datasets[0].data = data.results.graph_weight;
            configWeight.data.datasets[1].data = data.results.graph_weightAvg;
            configWeight.data.datasets[2].data = data.results.graph_weightTrend;
            configWeight.data.datasets[3].data = data.results.graph_weightGoal;

            var pointRadius = 3;
            if (data.results.graph_weight.length > 29) {
                pointRadius = 0;
            }

            configWeight.data.datasets[0].pointRadius = pointRadius;
            configWeight.data.datasets[1].pointRadius = pointRadius;
            configWeight.data.datasets[2].pointRadius = pointRadius;
            configWeight.data.datasets[3].pointRadius = pointRadius;

            var dataTimeScale = [];
            for ( var i = 0; i < data.results.graph_weight.length; i++ ) {
                dataTimeScale.push( reportDate.getTime() - i * 86400000 );
            }
            configWeight.data.labels = dataTimeScale;

            weightChart.update();
            trendWeight($, data);

            debug_add_gen_time("weight " + val, data.time);
        } );
    }

    function graphWeight($, data, st) {
        var i, dataSet1, dataSet2, dataSet3, dataSet4, dataTimeScale, aniDuration, pointRadius, weight_units;

        var weightGraph = $('#weightGraph');
        if (weightGraph.length > 0) {
            var $progress = $('#animationWeightProgress');

            $progress.show();

            aniDuration = 3000;

            dataSet1 = data.results.graph_weight;
            dataSet2 = data.results.graph_weightTrend;
            dataSet3 = data.results.graph_weightAvg;
            dataSet4 = data.results.graph_weightGoal;

            if (data.results.weight_units == "kg") {
                weight_units = "Kilograms";
            } else if (data.results.weight_units == "lb") {
                weight_units = "Pounds";
            } else {
                weight_units = data.results.weight_units;
            }

            if (dataSet1.length > 29) {
                pointRadius = 0;
            } else {
                pointRadius = 3;
            }

            dataTimeScale = [];
            for ( i = 0; i < dataSet1.length; i++ ) {
                dataTimeScale.push( st - i * 86400000 );
            }

            configWeight = {
                type: 'line',
                options: {
                    responsive: true,
                    title:{
                        display:true,
                        text:'Body Weight Chart'
                    },
                    tooltips: {
                        mode: 'label',
                        callbacks: {
                            beforeBody: function() {
                                return 'Weights in ' + weight_units;
                            }
                        }
                    },
                    hover: {
                        mode: 'dataset'
                    },
                    animation: {
                        duration: aniDuration,
                        onProgress: function(animation) {
                            $progress.attr({
                                value: animation.animationObject.currentStep / animation.animationObject.numSteps,
                            });
                        },
                        onComplete: function(animation) {
                            window.setTimeout(function() {
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
    }

    function trendWeight($, data) {
        var WeightTrend = $('#WeightTrend');

        if (WeightTrend.length > 0) {
            var classTable = "", htmlString = "";

            if (data.results.loss_rate_weight.length != 0) {
                htmlString += "<p>The table bellow shows your month on month weight changes</p>";
                htmlString += "<table class=\"table table-striped\">";
                htmlString += "<tr><th>Date</th><th>Monthly Trend</th></tr>";

                for (var key in data.results.loss_rate_weight) {
                    if (data.results.loss_rate_weight[key] <= 0) {
                        classTable = ' class="success"';
                    } else if (data.results.loss_rate_weight[key] >= 0.1) {
                        classTable = ' class="danger"';
                    } else {
                        classTable = "";
                    }
                    htmlString += "<tr"+classTable+"><td>" + key + "</td><td>" + data.results.loss_rate_weight[key] + " " + data.results.weight_units + " </td></tr>";
                }

                htmlString += "</table>";
            }

            if (weightTrends == '') {
                $.getJSON("../json.php?user=" + fitbitUserId + "&data=trend", function (trendData) {
                    weightTrends = '<p>Your last recorded weight was <strong>' + trendData.results.weight + '</strong>, so still has another'+
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