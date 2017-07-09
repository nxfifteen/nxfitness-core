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

    var JourneyBlock = $('#Journey');
    if (JourneyBlock.length > 0) {
        $.getJSON("../json.php?user=" + fitbitUserId + "&data=Journeys", function (data) {
            if (data.results.msg !== "Not on any jounry") {
                $('#NoJourney').remove();

                var nextLeg = true;
                var listGroup = '';
                listGroup += '<div class="list-group">';
                $.each(data.results[1].legs[1], function (index, leg) {
                    if (leg.miles_off === 0) {
                        //listGroup += ' <button type="button" style="width:100%;" class="list-group-item list-group-item-action" disabled>' + leg.subtitle + ', ' + leg.miles + ' miles travelled</button>';
                    } else {
                        listGroup += '<a href="#" class="list-group-item list-group-item-action flex-column align-items-start';
                        if (nextLeg) {
                            listGroup += ' active';
                            nextLeg = false;
                        }
                        listGroup += '">';
                        listGroup += '    <div class="d-flex w-100 justify-content-between">';
                        listGroup += '        <h5 class="mb-1">' + leg.subtitle + '</h5>';
                        listGroup += '        <small>' + leg.miles + ' miles travelled</small>';
                        listGroup += '    </div>';
                        listGroup += '    <p class="mb-1">' + leg.narrative + '</p>';
                        listGroup += '</a>';
                    }
                });
                listGroup += '</div>';
                $('#JourneyLegBlock').html(listGroup);

                $('#JourneyProgress').attr('aria-valuenow', data.results[1].legs_progress[1]).css('width', data.results[1].legs_progress[1] + '%');
                $.getJSON("../json.php?user=" + fitbitUserId + "&data=JourneysState", function (data) {
                    $('#JourneyName').html("<strong>" + data.results[1].name + "</strong> <small>" + data.results[1].blurb + "</small>");
                    $('#JourneyBlock').html(
                        "<em>" + data.results[1].legs.last.subtitle + "</em> <strong>" + data.results[1].legs.last.legs_names + "</strong> <small>" + data.results[1].legs.last.miles + " miles</small><br />" + data.results[1].legs.last.narrative + "<hr />" +
                        "<em>" + data.results[1].legs.next.subtitle + "</em> <strong>" + data.results[1].legs.next.legs_names + "</strong> <small>" + data.results[1].legs.next.miles + " miles</small><br />" + data.results[1].legs.next.narrative
                    );
                } );

            } else {
                JourneyBlock.remove();
            }
        } );
    }
});
