$(function () {
    'use strict';

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=SyncState", function (data) {

        var colour;
        var html = '';

        if (data.results.SyncProgress >= 98) {
            colour = 'success';
        } else if (data.results.SyncProgress >= 75) {
            colour = 'primary';
        } else if (data.results.SyncProgress >= 50) {
            colour = 'info';
        } else if (data.results.SyncProgress >= 25) {
            colour = 'warning';
        } else {
            colour = 'danger';
        }

        html += '<div class="col-12">';
        html += '<div class="card card-inverse card-' + colour + '">';
        html += '    <div class="card-block">';
        html += '        <div id="gaugeStepsText" class="h4 mb-0">' + data.results.SyncProgress + '</div>';
        html += '        <small class="text-muted text-uppercase font-weight-bold">Sync Progress</small>';
        html += '        <div class="progress progress-white progress-xs mt-3">';
        html += '            <div class="progress-bar" role="progressbar" style="width: ' + data.results.SyncProgress + '%" aria-valuenow="' + data.results.SyncProgress + '" aria-valuemin="0" aria-valuemax="100"></div>';
        html += '        </div>';
        html += '    </div>';
        html += '</div>';
        html += '</div>';
        $('#syncTotalProgress').html(html);

        html = '';
        var syncProgress = $('#syncProgress');
        $.each(data.results.SyncProgressScopes, function (index, scope) {

            if (scope.precentage >= 98) {
                colour = 'success';
            } else if (scope.precentage >= 98) {
                colour = 'primary';
            } else if (scope.precentage >= 75) {
                colour = 'info';
            } else if (scope.precentage >= 25) {
                colour = 'warning';
            } else {
                colour = 'danger';
            }

            html += '<div class="col-6 col-md-3">';
            html += '<div class="card card-inverse card-' + colour + '">';
            html += '    <div class="card-block">';
            html += '        <div id="gaugeStepsText" class="h4 mb-0">' + scope.precentage + '</div>';
            html += '        <small class="text-muted text-uppercase font-weight-bold">' + scope.name + '</small>';
            html += '        <div class="progress progress-white progress-xs mt-3">';
            html += '            <div class="progress-bar" role="progressbar" style="width: ' + scope.precentage + '%" aria-valuenow="' + scope.precentage + '" aria-valuemin="0" aria-valuemax="100"></div>';
            html += '        </div>';
            html += '    </div>';
            html += '</div>';
            html += '</div>';
        });
        syncProgress.html(html);


    });

});