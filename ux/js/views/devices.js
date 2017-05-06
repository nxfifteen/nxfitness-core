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

        $.each(devices, function (i, device) {
            html += '<tr>';
            /** @namespace device.deviceVersion */
            /** @namespace device.imageSmall */
            html += '    <td><img class="hidden-sm-down" src="../' + device.imageSmall + '" style="width: 35%;height: 35%;"><span class="hidden-md-up">' + device.deviceVersion + '<br />' + device.type + '</span></td>';
            /** @namespace device.alertTime */
            //noinspection JSValidateTypes
            if (device.alertTime === 1) {
                /** @namespace device.lastSyncTime */
                html += '    <td class="badge-warning">' + device.lastSyncTime + '</td>';
            } else {
                html += '    <td>' + device.lastSyncTime + '</td>';
            }
            /** @namespace device.battery */
            /** @namespace device.charges */
            html += '    <td>' + device.battery + ' <br /> Charged ' + device.charges + ' times.</td>';
            html += '</tr>';

            /** @namespace device.precentage */
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