$(function () {
    'use strict';

    /** @namespace data.results.badges */
    /** @namespace badgeTypeValue.timesAchieved */
    /** @namespace badgeTypeValue.badgeGradientStartColor */
    /** @namespace badgeTypeValue.marketingdescription */
    /** @namespace badgeTypeValue.badgeGradientEndColor */

    var marginTop;
    var myBadges = $('#myBadges');
    if (myBadges.length > 0) {
        $.getJSON("../json.php?user=" + fitbitUserId + "&data=Badges", function (data) {
            var html = '', htmlBadges, badgeGradientStartColor, badgeGradientEndColor, last_badge, last_badge_name,
                last_badge_marketingdescription;
            $.each(data.results.badges, function (badgeCat, badgeType) {
                htmlBadges = '<ul class="pop-badge">';
                $.each(badgeType, function (i, badgeTypeValue) {
                    if (i === 0) {
                        marginTop = '0px';
                    } else {
                        marginTop = '-50px';
                    }

                    badgeGradientStartColor = badgeTypeValue.badgeGradientStartColor;
                    badgeGradientEndColor = badgeTypeValue.badgeGradientEndColor;

                    htmlBadges += '<li style="margin-left: ' + marginTop + ';z-index: ' + i + ';margin-bottom: 0;"><a href="#"><img alt="' + badgeTypeValue.name + '" title="' + badgeTypeValue.name + '" src="' + '../' + data.results.images + '/100px/' + badgeTypeValue.image + '" alt="" class="img-responsive"><span style="z-index: 50; border-color: #' + badgeTypeValue.badgeGradientEndColor + '; background: #' + badgeGradientStartColor + ';background: -moz-linear-gradient(-45deg, #' + badgeGradientStartColor + ' 0%, #' + badgeGradientEndColor + ' 100%);background: -webkit-gradient(linear, left top, right bottom, color-stop(0%,#' + badgeGradientStartColor + '), color-stop(100%,#' + badgeGradientEndColor + '));background: -webkit-linear-gradient(-45deg, #' + badgeGradientStartColor + ' 0%,#' + badgeGradientEndColor + ' 100%);background: -o-linear-gradient(-45deg, #' + badgeGradientStartColor + ' 0%,#' + badgeGradientEndColor + ' 100%);background: -ms-linear-gradient(-45deg, #' + badgeGradientStartColor + ' 0%,#' + badgeGradientEndColor + ' 100%);background: linear-gradient(135deg, #' + badgeGradientStartColor + ' 0%,#' + badgeGradientEndColor + ' 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#' + badgeGradientStartColor + '\', endColorstr=\'#' + badgeGradientEndColor + '\',GradientType=1 ); color: #FFFFFF;"><strong>' + badgeTypeValue.dateTime + '</strong><br/>' + badgeTypeValue.marketingdescription + '<br/>(' + badgeTypeValue.timesAchieved + ' times)</span></a></li>';

                    last_badge = '../' + data.results.images + '/125px/' + badgeTypeValue.image;
                    last_badge_name = badgeTypeValue.name;
                    last_badge_marketingdescription = badgeTypeValue.marketingdescription;
                });
                htmlBadges += '</ul>';

                html += '<div class="row">';
                html += '<div class="col-12">';
                html += '<section class="card">';
                html += '<header class="card-header">';
                html += badgeCat;
                html += '</header>';
                html += '<div class="card-block">';
                html += '<div class="row" style="margin-bottom: 30px;">';
                html += '<div class="hidden-sm-down col-8 wrap-badge">';
                html += htmlBadges;
                html += '</div>';
                html += '<div class="col-12 col-sm-4" style="text-align: center">';
                html += '<img alt="' + last_badge_name + '" title="' + last_badge_name + '" src="' + last_badge + '"><br />' + last_badge_marketingdescription;
                html += '</div>';
                html += '</div>';
                html += '</div>';
                html += '</section>';
                html += '</div>';
                html += '</div>';
            });
            myBadges.html(html);

            debug_add_gen_time("badges", data.time);
        });

    }
});