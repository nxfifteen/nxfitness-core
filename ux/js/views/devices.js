/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 * https://nxfifteen.me.uk
 *
 * Copyright (c) 2017, Stuart McCulloch Anderson
 *
 * Released under the MIT license
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

$(function () {
    'use strict';

    $.getJSON("../json.php?user=" + fitbitUserId + "&data=Devices", function (data) {
        var html = '';
        var devicesBlock = $('#devices');
        var devices = data.results;
        /** @namespace device.deviceVersion */
        /** @namespace device.imageSmall */
        /** @namespace device.lastSyncTime */
        /** @namespace device.battery */
        /** @namespace device.charges */
        /** @namespace device.precentage */

        $.each(devices, function (i, device) {
            html += '<tr>';
            html += '    <td rowspan="2" width="200px"><img class="hidden-sm-down" src="../' + device.imageSmall + '" class="img-fluid"></td>';
            /** @namespace device.alertTime */
            if (device.alertTime === '1') {
                html += '    <td class="bg-success">';
            } else {
                html += '    <td>';
            }
            html += '<span>' + device.deviceVersion + '<br />' + device.type + '</span><br />' + device.lastSyncTime + '</td>';
            html += '    <td>' + device.battery + ' <br /> Charged ' + device.charges + ' times.</td>';
            html += '</tr>';

            if (device.precentage < 50) {
                html += '<tr class="bg-danger">';
            } else {
                html += '<tr>';
            }
            html += '    <td colspan="2">';
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