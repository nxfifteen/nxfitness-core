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

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=Account", function (data) {
        /** @namespace data.results.tweak */
        /** @namespace data.results.tweak.desire_steps */
        /** @namespace data.results.tweak.desire_steps_min */
        /** @namespace data.results.tweak.desire_steps_max */
        /** @namespace data.results.tweak.current_steps */
        /** @namespace data.results.tweak.habitica */
        /** @namespace data.results.tweak.habitica.habitica_user_id */
        /** @namespace data.results.tweak.habitica.habitica_api_key */
        /** @namespace data.results.tweak.habitica.habitia_switches */

        var switches = '<div class="row">';
        $.each(data.results.babel, function (babelKey, babelValues) {
            switches += '<div class="col-9 col-md-2">';
            switches += '<span class="form-control-label" style="padding-right: 10px">'+babelValues.name+'</span>';
            switches += '</div>';
            switches += '<div class="col-3 col-md-1">';
            switches += '<label class="switch switch-text switch-pill switch-success" style="margin-right: 10px">';
            switches += '  <input type="checkbox" class="switch-input" name="'+babelValues.name+'" id="'+babelKey+'" onchange="submitSwitch(this)"';
            if (babelValues.status) {
                switches += ' checked';
            }
            switches += '  >';
            switches += '  <span class="switch-label" data-on="On" data-off="Off"></span>';
            switches += '  <span class="switch-handle"></span>';
            switches += '</label>';
            switches += '</div>';
        });
        switches += '</div>';
        $('#ActiveIntents').html(switches);

        $('#currentStepGoal').html(data.results.tweak.current_steps);

        $("#minimumSteps").val(data.results.tweak.desire_steps_min);
        $("#maximumSteps").val(data.results.tweak.desire_steps_max);

        // With JQuery
        $("#ex2").slider({
            value: data.results.tweak.desire_steps,
            ticks: [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100, 105, 110],
            ticks_labels: ['0%', '5%', '10%', '', '20%', '', '30%', '', '40%', '', '50%', '', '60%', '', '70%', '', '80%', '', '90%', '', '100%', '', '110%'],
            ticks_snap_bounds: 1
        });

        var output = [];
        var outputDefault = '<option>Select a new Journey</option>';

        $.each(data.results.journeys, function(key, journey)
        {
            var selected = '';
            if (journey.jid === data.results.journey.jid) {
                selected = 'selected';
                outputDefault = '<option selected>Select a new Journey</option>';
            }

            output.push('<option '+selected+'>'+ journey.name +'</option>');
        });

        $('#selectedJourney').html(outputDefault + output.join(''));

        if (data.results.habitica.length === 0) {
            $('#habiticaSettings').remove();
        } else {
            var habiticaSwitches = '<div class="row">';
            $.each(data.results.habitica.habitia_switches, function (babelKey, babelValues) {
                habiticaSwitches += '<div class="col-9 col-md-2">';
                habiticaSwitches += '<span class="form-control-label" style="padding-right: 10px">' + babelValues.name + '</span>';
                habiticaSwitches += '</div>';
                habiticaSwitches += '<div class="col-3 col-md-1">';
                habiticaSwitches += '<label class="switch switch-text switch-pill switch-success" style="margin-right: 10px">';
                habiticaSwitches += '  <input type="checkbox" class="switch-input" name="' + babelValues.name + '" id="' + babelKey + '" onchange="submitHabiticaSwitch(this)"';
                if (babelValues.status !== '0') {
                    habiticaSwitches += ' checked';
                }
                habiticaSwitches += '  >';
                habiticaSwitches += '  <span class="switch-label" data-on="On" data-off="Off"></span>';
                habiticaSwitches += '  <span class="switch-handle"></span>';
                habiticaSwitches += '</label>';
                habiticaSwitches += '</div>';
            });
            habiticaSwitches += '</div>';
            $('#habiticaSwitches').html(habiticaSwitches);

            $("#habitica_user_id").val(data.results.habitica.habitica_user_id);
            $("#habitica_api_key").val(data.results.habitica.habitica_api_key);
            $("#habitica_max_eggs").val(data.results.habitica.habitica_max_eggs);
            $("#habitica_max_potions").val(data.results.habitica.habitica_max_potions);

            $("#habitica_max_gems").val(data.results.habitica.habitica_max_gems);
            $("#habitica_min_gold").val(data.results.habitica.habitica_min_gold);

            var buyGems = '<div class="row">';
            buyGems += '<div class="col-2">';
            buyGems += '<span class="form-control-label" style="padding-right: 10px">Bye Gems</span>';
            buyGems += '</div>';
            buyGems += '<div class="col-10">';
            buyGems += '<label class="switch switch-text switch-pill switch-success" style="margin-right: 10px">';
            buyGems += '  <input type="checkbox" class="switch-input" name="habitica_bye_gems" id="habitica_bye_gems" onchange="submitBuyGems(this)"';
            if (data.results.habitica.habitica_bye_gems !== '0') {
                buyGems += ' checked';
                $('#gemForm').show();
            } else {
                $('#gemForm').hide();
            }
            buyGems += '  >';
            buyGems += '  <span class="switch-label" data-on="On" data-off="Off"></span>';
            buyGems += '  <span class="switch-handle"></span>';
            buyGems += '</label>';
            buyGems += '</div>';

            buyGems += '</div>';
            $('#buyGems').html(buyGems);
        }
    });

});

// this is the id of the form
$("#habiticaBuyGems").submit(function (e) {
    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#habiticaBuyGems").serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $('#habiticaReport').html(response);
    }).fail(function (response) {
        $('#habiticaReport').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

// this is the id of the form
$("#journeySelector").submit(function (e) {
    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#journeySelector").serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $('#journeyAlert').html(response);
    }).fail(function (response) {
        $('#journeyAlert').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

// this is the id of the form
$("#desireImprovment").submit(function (e) {
    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#desireImprovment").serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $('#desireMsg').html(response);
    }).fail(function (response) {
        $('#desireMsg').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

// this is the id of the form
$("#habiticaConnect").submit(function (e) {
    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#habiticaConnect").serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $('#habiticaReport').html(response);
    }).fail(function (response) {
        $('#habiticaReport').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

// this is the id of the form
$("#habiticaMaxItems").submit(function (e) {
    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#habiticaMaxItems").serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $('#habiticaReport').html(response);
    }).fail(function (response) {
        $('#habiticaReport').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

function submitBuyGems(e) {
    var url = "../ajax.php"; // the script where you handle the form input.

    var data = "formId=habiticaBuyGems&switch=" + e.id + "&value=" +  e.checked;

    if (e.checked) {
        $('#gemForm').show();
    } else {
        $('#gemForm').hide();
    }

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        if (e.checked) {
            $('#habiticaReport').html("Turned On Auto Purchasing Gems");
        } else {
            $('#habiticaReport').html("Turned Off Auto Purchasing Gems");
        }
    }).fail(function (response) {
        $('#habiticaReport').html(response.responseText);
    });
}

function submitSwitch(e) {
    var url = "../ajax.php"; // the script where you handle the form input.

    var data = "formId=intentSwitch&switch=" + e.id + "&value=" +  e.checked;

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        if (e.checked) {
            $('#ActiveIntentsReport').html("Enabled " + e.name);
        } else {
            $('#ActiveIntentsReport').html("Disabled " + e.name);
        }
    }).fail(function (response) {
        $('#ActiveIntentsReport').html(response.responseText);
    });
}

function submitHabiticaSwitch(e) {
    var url = "../ajax.php"; // the script where you handle the form input.

    var data = "formId=habiticaSwitches&switch=" + e.id + "&value=" +  e.checked;

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        if (e.checked) {
            $('#habiticaReport').html("Enabled " + e.name);
        } else {
            $('#habiticaReport').html("Disabled " + e.name);
        }
    }).fail(function (response) {
        $('#habiticaReport').html(response.responseText);
    });
}
