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

    var calendar;

    var NoPush = $('#NoPush');
    var Push = $('#Push');
    if (Push.length > 0) {
        $.getJSON("../json.php?user=" + fitbitUserId + "&data=Push", function (data) {
            if (data.results.pushActive === "active") {
                NoPush.remove();

                $('#PushName').html("<strong>" + data.results.next.startDateF + "</strong> till <strong>" + data.results.next.endDateF + "</strong>");
                $('#PushBlock').html(
                    "Your step goal for this push is " + data.results.goals.Steps + " steps a day. Your on day " + data.results.current.day + " of your " + data.results.pushLength + " day push, and have betten your goal for " + data.results.current.day_past + " days."
                );
                $('#PushProgress').attr('aria-valuenow', data.results.current.score).css('width', data.results.current.score + '%');

                $('#PushNameCal').html("Push Calendar - <strong>" + data.results.next.startDateF + "</strong>");
                calendar = $("#calendar").calendar({
                    tmpl_path: "./inc/tmpls/",
                    tmpl_cache: false,
                    modal: "#events-modal",
                    modal_type : "template",
                    view: 'month',
                    events_source: "../json.php?user=" + fitbitUserId + "&data=PushCalendar&start=" + data.results.next.startDate + "&end=" + data.results.next.endDate,
                    onAfterViewLoad: function (view) {
                        $('.btn-group button').removeClass('active');
                        $('button[data-calendar-view="' + view + '"]').addClass('active');
                    },
                    classes: {
                        months: {
                            general: 'label'
                        }
                    }
                });

            } else if (data.results.pushActive === "future") {
                NoPush.remove();
                $('#PushName').html("<strong>" + data.results.next.startDateF + "</strong> till <strong>" + data.results.next.endDateF + "</strong>");
                $('#PushBlock').html(
                    "Your next " + data.results.pushLength + " day push will start on " + data.results.next.startDateF + " day push. Your target will be " + data.results.goals.Steps + " steps a day."
                );
                $('#PushFooter').remove();

                $('#PushNameCal').html("Push Calendar - <strong>" + data.results.next.startDateF + "</strong>");
                calendar = $("#calendar").calendar({
                    day: data.results.last.startDate,
                    tmpl_path: "./inc/tmpls/",
                    tmpl_cache: false,
                    modal: "#events-modal",
                    modal_type : "template",
                    view: 'month',
                    events_source: "../json.php?user=" + fitbitUserId + "&data=PushCalendar&start=" + data.results.last.startDate + "&end=" + data.results.last.endDate,
                    onAfterViewLoad: function (view) {
                        $('.btn-group button').removeClass('active');
                        $('button[data-calendar-view="' + view + '"]').addClass('active');
                    },
                    classes: {
                        months: {
                            general: 'label'
                        }
                    }
                });
            } else {
                Push.remove();
            }
        } );
    }

    $('.btn-group button[data-calendar-view]').each(function () {
        var $this = $(this);
        $this.click(function () {
            calendar.view($this.data('calendar-view'));
        });
    });
});
