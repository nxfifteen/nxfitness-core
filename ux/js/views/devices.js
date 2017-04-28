$(function () {
    'use strict';

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=Devices", function (data) {
        var html = '';
        var devicesBlock = $('#devices');
        var devices = data.results;

        $.each(devices, function (i, device) {
            html += '<tr>';
            html += '    <td><img class="hidden-sm-down" src="../' + device.imageSmall + '" style="width: 35%;height: 35%;"><span class="hidden-md-up">' + device.deviceVersion + '<br />' + device.type + '</span></td>';
            if (device.alertTime === 1) {
                html += '    <td class="badge-warning">' + device.lastSyncTime + '</td>';
            } else {
                html += '    <td>' + device.lastSyncTime + '</td>';
            }
            html += '    <td>' + device.battery + ' <br /> Charged ' + device.charges + ' times.</td>';
            html += '</tr>';

            if (device.precentage < 50) {
                html += '<tr class="badge-danger">';
            } else {
                html += '<tr>';
            }
            html += '    <td colspan="3">';
            html += '        <div class="progress">';
            html += '            <div class="progress-bar" role="progressbar" style="width: ' + device.precentage + '%" aria-valuenow="' + device.precentage + '" aria-valuemin="0" aria-valuemax="100"></div>';
            html += '        </div>';
            html += '    </td>';
            html += '</tr>';

        });

        devicesBlock.html(html);

        debug_add_gen_time("devices", data.time);
    });
});