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
    });

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
