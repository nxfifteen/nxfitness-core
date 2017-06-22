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
        $('#fullName').html(data.results.name);
        $('#fuid').html(data.results.fuid);
        $('#email').val(data.results.eml);
        $('#apiKey').html(data.results.api);
    });
});

// this is the id of the form
$("#accDeletion").submit(function (e) {

    $('#dangerModal').modal('hide');

    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#accDeletion").serialize();

    var html = '';
    html += '<p>I\'m currently deleting your account.</p>';
    html += '<p>This process can take 20/30 seconds, and you dont have to stick around for it. Once complete this page will refresh and you will be logged out.</p>';
    $('#actionsText').html(html).show();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        window.location.href = "views/pages/logout";
    }).fail(function (response) {
        $('#actionsText').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

// this is the id of the form
$("#passwordChange").submit(function (e) {
    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#passwordChange").serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $('#passwordChangeReport').html(response);
    }).fail(function (response) {
        $('#passwordChangeReport').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

// this is the id of the form
$("#basicProfile").submit(function (e) {
    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#basicProfile").serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $('#basicProfileReport').html("Profile Updated");
    }).fail(function (response) {
        $('#basicProfileReport').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});

// this is the id of the form
$("#apiKeyRefresh").submit(function (e) {
    var url = "../ajax.php"; // the script where you handle the form input.
    var data = $("#apiKeyRefresh").serialize();

    $.ajax({
        type: "POST",
        url: url,
        data: data
    }).done(function (response) {
        $('#apiKey').html(response);
    }).fail(function (response) {
        $('#apiKey').html(response.responseText);
    });

    e.preventDefault(); // avoid to execute the actual submit of the form.
});
