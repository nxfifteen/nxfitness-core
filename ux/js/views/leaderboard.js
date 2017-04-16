$(function () {
    'use strict';

    var leaderboard = $('#leaderboard');
    if (leaderboard.length > 0) {
        $.getJSON("../json.php?user="+fitbitUserId+"&data=Leaderboard&period=last30", function (data) {
            var html = '';
            html += '<div class="row">';
            $.each(data.results.friends, function (index, friend) {
                html += '<div class="col-12 col-md-4 col-xl-3">';
                html += '    <div class="card';
                //noinspection EqualityComparisonWithCoercionJS
                if (index == fitbitUserId) { html += ' alert-success'; }
                html += '        ">';
                html += '        <div class="card-block">';
                html += '            <div class="row">';
                html += '                <div class="col-12 col-xl-7">';
                html += '                    <div class="h4 mb-0">' + friend.displayName + '</div>';
                html += '                    <small class="text-muted text-uppercase font-weight-bold">';
                html += '                        Average - ' + friend.stepsAvg + '<br />';
                html += '                        Life Time - ' + friend.stepsLife + '<br />';
                html += '                        Last 7-days - ' + friend.stepsSum + '<br />';
                html += '                        <br />';
                html += '                    </small>';
                html += '                </div>';
                html += '                <div class="col-xl-5 hidden-lg-down">';
                html += '                    <img class="img-fluid" src="' + friend.avatar + '" />';
                html += '                </div>';
                html += '            </div>';
                html += '        </div>';
                html += '    </div>';
                html += '</div>';
            });
            html += '</div>';

            leaderboard.html(html);

            var activeFriends = $('#activeFriends');
            if (activeFriends.length > 0) {
                activeFriends.html(data.results.activeFriends);
            }

            var totalFriends = $('#totalFriends');
            if (totalFriends.length > 0) {
                totalFriends.html(data.results.totalFriends);
            }

            debug_add_gen_time("leaderboard", data.time);
        });
    }
});