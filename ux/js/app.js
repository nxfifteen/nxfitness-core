/*****
 * CONFIGURATION
 */
// Active ajax page loader
$.ajaxLoad = true;

//required when $.ajaxLoad = true
$.defaultPage = 'main.html';
$.subPagesDirectory = 'views/';
$.page404 = 'views/pages/404.html';
$.mainContent = $('#ui-view');

//Main navigation
$.navigation = $('nav > ul.nav');

$.panelIconOpened = 'icon-arrow-up';
$.panelIconClosed = 'icon-arrow-down';

//Default colours
$.brandPrimary = '#20a8d8';
$.brandSuccess = '#4dbd74';
$.brandInfo = '#63c2de';
$.brandWarning = '#f8cb00';
$.brandDanger = '#f86c6b';

$.grayDark = '#2a2c36';
$.gray = '#55595c';
$.grayLight = '#818a91';
$.grayLighter = '#d1d4d7';
$.grayLightest = '#f8f9fa';

'use strict';

/*****
 * ASYNC LOAD
 * Load JS files and CSS files asynchronously in ajax mode
 */
function loadJS(jsFiles, pageScript) {

    var i;
    var body;
    var script;
    for (i = 0; i < jsFiles.length; i++) {

        body = document.getElementsByTagName('body')[0];
        script = document.createElement('script');
        script.type = 'text/javascript';
        script.async = false;
        script.src = jsFiles[i];
        body.appendChild(script);
    }

    if (pageScript) {
        body = document.getElementsByTagName('body')[0];
        script = document.createElement('script');
        script.type = 'text/javascript';
        script.async = false;
        script.src = pageScript;
        body.appendChild(script);
    }

    init();
}

function loadCSS(cssFile, end, callback) {

    var s;
    var head;
    var cssArray = {};

    if (!cssArray[cssFile]) {
        cssArray[cssFile] = true;

        if (end === 1) {

            head = document.getElementsByTagName('head')[0];
            s = document.createElement('link');
            s.setAttribute('rel', 'stylesheet');
            s.setAttribute('type', 'text/css');
            s.setAttribute('href', cssFile);

            s.onload = callback;
            head.appendChild(s);

        } else {

            head = document.getElementsByTagName('head')[0];
            var style = document.getElementById('main-style');

            s = document.createElement('link');
            s.setAttribute('rel', 'stylesheet');
            s.setAttribute('type', 'text/css');
            s.setAttribute('href', cssFile);

            s.onload = callback;
            head.insertBefore(s, style);

        }

    } else if (callback) {
        callback();
    }

}

/****
 * AJAX LOAD
 * Load pages asynchronously in ajax mode
 */

if ($.ajaxLoad) {

    //noinspection JSUnusedGlobalSymbols
    var paceOptions = {
        elements: false,
        restartOnRequestAfter: false
    };

    var url = location.hash.replace(/^#/, '');

    if (url !== '') {
        setUpUrl(url);
    } else {
        setUpUrl($.defaultPage);
    }

    $(document).on('click', '.nav a[href!="#"]', function (e) {
        var target = $(e.currentTarget);
        if (target.attr('href').indexOf('logout') !== -1 || target.attr('href').indexOf('refresh') !== -1) {
            e.preventDefault();
            window.location = (target.attr('href'));
        } else if ($(this).parent().parent().hasClass('nav-tabs') || $(this).parent().parent().hasClass('nav-pills')) {
            e.preventDefault();
        } else if ($(this).attr('target') === '_top' ) {
            e.preventDefault();
            window.location = (target.attr('href'));
        } else if ($(this).attr('target') === '_blank') {
            e.preventDefault();
            window.open(target.attr('href'));
        } else {
            e.preventDefault();
            setUpUrl(target.attr('href'));
        }
    });

    $(document).on('click', 'a[href="#"]', function (e) {
        e.preventDefault();
    });
}

function setUpUrl(url) {

    $('nav .nav li .nav-link').removeClass('active');
    $('nav .nav li.nav-dropdown').removeClass('open');
    $('nav .nav li:has(a[href="' + url.split('?')[0] + '"])').addClass('open');
    $('nav .nav a[href="' + url.split('?')[0] + '"]').addClass('active');

    loadPage(url);
}

function loadPage(url) {

    $.ajax({
        type: 'GET',
        url: $.subPagesDirectory + url,
        dataType: 'html',
        cache: false,
        async: false,
        beforeSend: function () {
            $.mainContent.css({opacity: 0});
        },
        success: function () {
            Pace.restart();
            $('html, body').animate({scrollTop: 0}, 0);
            //noinspection JSUnusedLocalSymbols
            $.mainContent.load($.subPagesDirectory + url, null, function (responseText) {
                window.location.hash = url;
            }).delay(250).animate({opacity: 1}, 0);
        },
        error: function () {
            window.location.href = $.page404;
        }

    });
}

function setUpBreadcrumb(urls, active) {

    var i;
    var breadcrumb = $('.breadcrumb');
    var crumbs = '';
    // var crumbAppend = breadcrumb.html();
    for (i = 0; i < urls.length; i++) {
        crumbs += '<li class="breadcrumb-item">' + urls[i] + '</li>';
    }

    crumbs += '<li class="breadcrumb-item active">' + active + '</li>';

    // breadcrumb.html(crumbs + crumbAppend);
    breadcrumb.html(crumbs);

}
/****
 * MAIN NAVIGATION
 */

$(document).ready(function ($) {

    // Add class .active to current link - AJAX Mode off
    $.navigation.find('a').each(function () {

        var cUrl = String(window.location).split('?')[0];

        if (cUrl.substr(cUrl.length - 1) === '#') {
            cUrl = cUrl.slice(0, -1);
        }

        if ($($(this))[0].href === cUrl) {
            $(this).addClass('active');

            $(this).parents('ul').add(this).each(function () {
                $(this).parent().addClass('open');
            });
        }
    });

    // Dropdown Menu
    $.navigation.on('click', 'a', function (e) {

        if ($.ajaxLoad) {
            e.preventDefault();
        }

        if ($(this).hasClass('nav-dropdown-toggle')) {
            $(this).parent().toggleClass('open');
            resizeBroadcast();
        }
    });

    function resizeBroadcast() {

        var timesRun = 0;
        var interval = setInterval(function () {
            timesRun += 1;
            if (timesRun === 5) {
                clearInterval(interval);
            }
            window.dispatchEvent(new Event('resize'));
        }, 62.5);
    }

    /* ---------- Main Menu Open/Close, Min/Full ---------- */
    $('.navbar-toggler').click(function () {

        if ($(this).hasClass('sidebar-toggler')) {
            $('body').toggleClass('sidebar-hidden');
            resizeBroadcast();
        }

        if ($(this).hasClass('sidebar-minimizer')) {
            $('body').toggleClass('sidebar-compact');
            resizeBroadcast();
        }

        if ($(this).hasClass('aside-menu-toggler')) {
            $('body').toggleClass('aside-menu-hidden');
            resizeBroadcast();
        }

        if ($(this).hasClass('mobile-sidebar-toggler')) {
            $('body').toggleClass('sidebar-mobile-show');
            resizeBroadcast();
        }

    });

    $('.sidebar-close').click(function () {
        $('body').toggleClass('sidebar-opened').parent().toggleClass('sidebar-opened');
    });

    /* ---------- Disable moving to top ---------- */
    $('a[href="#"][data-top!=true]').click(function (e) {
        e.preventDefault();
    });

});

/****
 * CARDS ACTIONS
 */

$(document).on('click', '.card-actions a', function (e) {
    e.preventDefault();

    if ($(this).hasClass('btn-close')) {
        $(this).parent().parent().parent().fadeOut();
    } else if ($(this).hasClass('btn-minimize')) {
        //noinspection JSUnusedLocalSymbols
        var $target = $(this).parent().parent().next('.card-block');
        if (!$(this).hasClass('collapsed')) {
            $('i', $(this)).removeClass($.panelIconOpened).addClass($.panelIconClosed);
        } else {
            $('i', $(this)).removeClass($.panelIconClosed).addClass($.panelIconOpened);
        }

    } else if ($(this).hasClass('btn-setting')) {
        $('#myModal').modal('show');
    }

});

//noinspection JSUnusedGlobalSymbols
function capitalizeFirstLetter(string) {
    //noinspection JSUnresolvedFunction
    return string.charAt(0).toUpperCase() + string.slice(1);
}

//noinspection JSUnusedLocalSymbols
function init(url) {

    /* ---------- Tooltip ---------- */
    $('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement": "bottom", delay: {show: 400, hide: 200}});

    /* ---------- Popover ---------- */
    $('[rel="popover"],[data-rel="popover"],[data-toggle="popover"]').popover();

}

function returnDateString(dateObject) {
    var fullYear = dateObject.getFullYear().toString();
    var mm = (dateObject.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = dateObject.getDate().toString();

    return fullYear + "-" + (mm[1] ? mm : "0" + mm[0]) + "-" + (dd[1] ? dd : "0" + dd[0]);
}

function debug_add_gen_time($apiName, $timeValue) {
    var newBlock = '<div class="callout callout-warning m-0 py-3"><div>Gen Time <strong>' + $apiName +'</strong></div><small class="text-muted mr-3"><i class="icon-calendar"></i>&nbsp; ' + $timeValue +'</small></div><hr class="mx-3 my-0">';

    var debug_gen_time = $('#debug_gen_time');
    debug_gen_time.html(debug_gen_time.html() + newBlock);
}