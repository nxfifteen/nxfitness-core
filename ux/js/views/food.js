$(function () {
    'use strict';

    $('#reportedDate').html(returnDateString(new Date()));
    loadJson($, new Date(), "last1");

    $('#dateToday').bind('click', function () {
        $('#reportedDate').html(returnDateString(new Date()));
        loadJson($, new Date(), "last1");
    });

    function loadJson($, dateObject, period) {
        var datePrev = $('#datePrev');
        var dateNext = $('#dateNext');
        var datePrevMonth = $('#datePrevMonth');
        var datePrevWeek = $('#datePrevWeek');

        datePrev.unbind('click');
        dateNext.unbind('click');
        datePrevMonth.unbind('click');
        datePrevWeek.unbind('click');

        $.getJSON("../json.php?user=" + fitbitUserId + "&data=fooddiary&date=" + returnDateString(dateObject) + "&period=" + period, function (data) {

            var valueKilocalories = $('#valueKilocalories').find('.value');
            if (valueKilocalories.length > 0) {
                /** @namespace data.results.food.summary.calories */
                /** @namespace data.results.food */
                valueKilocalories.html(data.results.food.summary.calories);
            }

            var valueFat = $('#valueFat').find('.value');
            if (valueFat.length > 0) {
                valueFat.html(data.results.food.summary.fat);
            }

            var valueFiber = $('#valueFiber').find('.value');
            if (valueFiber.length > 0) {
                /** @namespace data.results.food.summary.fiber */
                valueFiber.html(data.results.food.summary.fiber);
            }

            var valueCarbs = $('#valueCarbs').find('.value');
            if (valueCarbs.length > 0) {
                /** @namespace data.results.food.summary.carbs */
                valueCarbs.html(data.results.food.summary.carbs);
            }

            var valueSodium = $('#valueSodium').find('.value');
            if (valueSodium.length > 0) {
                /** @namespace data.results.food.summary.sodium */
                valueSodium.html(data.results.food.summary.sodium);
            }

            var valueProtein = $('#valueProtein').find('.value');
            if (valueProtein.length > 0) {
                /** @namespace data.results.food.summary.protein */
                valueProtein.html(data.results.food.summary.protein);
            }

            var goalKilocalories = $('#goalKilocalories').find('.value');
            if (goalKilocalories.length > 0) {
                goalKilocalories.html(data.results.food.goals.calories);
            }

            var goalFat = $('#goalFat').find('.value');
            if (goalFat.length > 0) {
                goalFat.html(data.results.food.goals.fat);
            }

            var goalFiber = $('#goalFiber').find('.value');
            if (goalFiber.length > 0) {
                goalFiber.html(data.results.food.goals.fiber);
            }

            var goalCarbs = $('#goalCarbs').find('.value');
            if (goalCarbs.length > 0) {
                goalCarbs.html(data.results.food.goals.carbs);
            }

            var goalSodium = $('#goalSodium').find('.value');
            if (goalSodium.length > 0) {
                goalSodium.html(data.results.food.goals.sodium);
            }

            var goalProtein = $('#goalProtein').find('.value');
            if (goalProtein.length > 0) {
                goalProtein.html(data.results.food.goals.protein);
            }

            var remKilocalories = $('#remKilocalories');
            var remKilocaloriesValue = remKilocalories.find('.value');
            if (remKilocalories.length > 0) {
                remKilocaloriesValue.html(data.results.food.goals.calories - data.results.food.summary.calories);
                colourNutrion(remKilocalories, data.results.food.goals.calories, data.results.food.summary.calories);
            }

            var remFat = $('#remFat');
            var remFatValue = remFat.find('.value');
            if (remFat.length > 0) {
                remFatValue.html(data.results.food.goals.fat - data.results.food.summary.fat);
                colourNutrion(remFat, data.results.food.goals.fat, data.results.food.summary.fat);
            }

            var remFiber = $('#remFiber');
            var remFiberValue = remFiber.find('.value');
            if (remFiber.length > 0) {
                remFiberValue.html(data.results.food.goals.fiber - data.results.food.summary.fiber);
                colourNutrion(remFiber, data.results.food.goals.fiber, data.results.food.summary.fiber);
            }

            var remCarbs = $('#remCarbs');
            var remCarbsValue = remCarbs.find('.value');
            if (remCarbs.length > 0) {
                remCarbsValue.html(data.results.food.goals.carbs - data.results.food.summary.carbs);
                colourNutrion(remCarbs, data.results.food.goals.carbs, data.results.food.summary.carbs);
            }

            var remSodium = $('#remSodium');
            var remSodiumValue = remSodium.find('.value');
            if (remSodium.length > 0) {
                remSodiumValue.html(data.results.food.goals.sodium - data.results.food.summary.sodium);
                colourNutrion(remSodium, data.results.food.goals.sodium, data.results.food.summary.sodium);
            }

            var remProtein = $('#remProtein');
            var remProteinValue = remProtein.find('.value');
            if (remProtein.length > 0) {
                remProteinValue.html(data.results.food.goals.protein - data.results.food.summary.protein);
                colourNutrion(remProtein, data.results.food.goals.protein, data.results.food.summary.protein);
            }

            var foodDiary = $('#foodDiary');
            if (foodDiary.length > 0) {
                var html = '';
                html += '<table class="table table-hover" id="food-table-daily-totals">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>Meal</th>';
                html += '<th>Kilocalories</th>';
                html += '<th>Fat</th>';
                html += '<th class="d-sm-down-none">Fiber</th>';
                html += '<th>Carbs</th>';
                html += '<th class="d-sm-down-none">Sodium</th>';
                html += '<th>Protein</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                $.each(data.results.food.meals, function (meal, breakdown) {
                    html += '<tr>';
                    html += '<td>' + meal.replace(" Summary", "") + '</td>';
                    html += '<td style="text-align: center"><strong class="value">' + breakdown.calories + '</strong></td>';
                    html += '<td style="text-align: right"><span class="value">' + breakdown.fat + '</span> g</td>';
                    html += '<td class="d-sm-down-none" style="text-align: right"><span class="value">' + breakdown.fiber + '</span> g</td>';
                    html += '<td style="text-align: right"><span class="value">' + breakdown.carbs + '</span> g</td>';
                    html += '<td class="d-sm-down-none" style="text-align: right"><span class="value">' + breakdown.sodium + '</span> mg</td>';
                    html += '<td style="text-align: right"><span class="value">' + breakdown.protein + '</span> g</td>';
                    html += '</tr>';
                });
                html += '</tbody>';
                html += '</table>';

                foodDiary.html(html);
            }

            var mealPieChart = $('#mealPieChart');
            if (mealPieChart.length > 0) {
                var PieChartData = [];
                /** @namespace data.results.food.meals */
                $.each(data.results.food.meals, function (meal, breakdown) {
                    PieChartData[PieChartData.length] = {
                        label: meal,
                        data: breakdown.precentage
                    };
                });
                if (PieChartData.length > 0) {
                    $.plot(mealPieChart, PieChartData, {
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
                } else {
                    mealPieChart.html("");
                }
            }

            var splitPieChart = $('#splitPieChart');
            if (splitPieChart.length > 0) {
                var totalCarbs = data.results.food.summary.carbs;
                var totalFat = data.results.food.summary.fat;
                var totalProtein = data.results.food.summary.protein;
                var totalIntake = totalCarbs + totalFat + totalProtein;

                var splitPieChartData = [];
                splitPieChartData[0] = {
                    label: "Carbs",
                    data: (totalCarbs / totalIntake) * 100,
                    color: '#edc240'
                };
                splitPieChartData[1] = {
                    label: "Fat",
                    data: (totalFat / totalIntake) * 100,
                    color: '#cb4b4b'
                };
                splitPieChartData[2] = {
                    label: "Protein",
                    data: (totalProtein / totalIntake) * 100,
                    color: '#afd8f8'
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

            var consumptionLevel = $('.consLevel');
            if (consumptionLevel.length > 0) {
                /** @namespace data.results.water.liquid */
                /** @namespace data.results.water.goal */
                /** @namespace data.results.water */
                consumptionLevel.html(data.results.water.liquid + '/' + data.results.water.goal);
                var prec = (data.results.water.liquid / data.results.water.goal) * 100;
                consumptionLevel.css({"height": prec + "%"});
            }

            debug_add_gen_time("food diary " + period, data.time);
        });

        datePrevMonth.bind('click', function () {
            $('#reportedDate').html("Last Month");
            loadJson($, dateObject, "last30");
        });

        datePrevWeek.bind('click', function () {
            $('#reportedDate').html("Last Week");
            loadJson($, dateObject, "last7");
        });

        var dateObjYesterday = new Date(dateObject);
        dateObjYesterday.setDate(dateObject.getDate() - 1);
        datePrev.html('<span><i class="fa fa-arrow-left"></i></span> ' + returnDateString(dateObjYesterday));
        datePrev.bind('click', function () {
            $('#reportedDate').html(returnDateString(dateObjYesterday));
            loadJson($, dateObjYesterday, "last1");
        });

        var dateObjTomorrow = new Date(dateObject);
        dateObjTomorrow.setDate(dateObject.getDate() + 1);
        if ((dateObjTomorrow.getTime() / 1000) > (new Date().getTime() / 1000)) {
            dateNext.hide();
        } else {
            dateNext.show();
            dateNext.html('<span><i class="fa fa-arrow-right"></i></span> ' + returnDateString(dateObjTomorrow));
            dateNext.bind('click', function () {
                $('#reportedDate').html(returnDateString(dateObjTomorrow));
                loadJson($, dateObjTomorrow, "last1");
            });
        }
    }

    function colourNutrion(remFat, goals, summary) {
        if (summary > goals) {
            remFat.css({"color": "#cb4b4b"});
        } else if (summary > goals - (goals * 0.10)) {
            remFat.css({"color": "#edc240"});
        } else {
            remFat.css({"color": "#197910"});
        }
    }

    function returnDateString(dateObject) {
        var yyyy = dateObject.getFullYear().toString();
        var mm = (dateObject.getMonth() + 1).toString(); // getMonth() is zero-based
        var dd = dateObject.getDate().toString();

        return yyyy + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]);
    }

    function labelFormatter(label, series) {
        return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }
});