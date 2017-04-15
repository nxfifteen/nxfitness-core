$(function () {
    'use strict';

    $.getJSON("../json.php?user="+fitbitUserId+"&data=Devices", function (data) {
        var html = '';
        var devicesBlock = $('#devices');
        var devices = data.results;

        $.each(devices, function (i, device) {
            html += '<tr>';
            html += '    <td rowspan="2"><img src="../'+device.imageSmall+'" style="width: 75%;height: 75%;" /></td>';
            html += '    <td>'+device.deviceVersion+'<br />'+device.type+'</td>';
            if (device.alertTime === 1) {
                html += '    <td class="badge-warning">'+device.lastSyncTime+'</td>';
            } else {
                html += '    <td>'+device.lastSyncTime+'</td>';
            }
            html += '    <td>'+device.battery+' | Charged '+device.charges+' times.</td>';
            html += '</tr>';

            if (device.precentage < 50) {
                html += '<tr class="badge-danger">';
            } else {
                html += '<tr>';
            }
            html += '    <td colspan="3">';
            html += '        <div class="progress">';
            html += '            <div class="progress-bar" role="progressbar" style="width: '+device.precentage+'%" aria-valuenow="'+device.precentage+'" aria-valuemin="0" aria-valuemax="100"></div>';
            html += '        </div>';
            html += '    </td>';
            html += '</tr>';

        });

        devicesBlock.html(html);

        debug_add_gen_time("devices", data.time);
    });
});