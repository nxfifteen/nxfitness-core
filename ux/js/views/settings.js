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
    });
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
