<?php
/**
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Core
 * @subpackage  UX
 * @version     0.0.1.x
 * @since       0.0.0.1
 * @author      Stuart McCulloch Anderson <stuart@nxfifteen.me.uk>
 * @link        https://nxfifteen.me.uk NxFIFTEEN
 * @link        https://nxfifteen.me.uk/nxcore Project Page
 * @link        https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core Git Repo
 * @copyright   2017 Stuart McCulloch Anderson
 * @license     https://nxfifteen.me.uk/api/license/mit/2015-2017 MIT
 */

if (is_array($_SERVER) && array_key_exists("REDIRECT_URL", $_SERVER)) {
    /** @noinspection PhpUndefinedMethodInspection */
    $url = str_replace($App->getConfig("/admin"), "", $_SERVER['REDIRECT_URL']);
} else {
    $url = "";
}

/** @noinspection PhpUndefinedMethodInspection */
if ($_COOKIE['_nx_fb_usr'] == $App->getNxFit()->getSetting('ownerFuid', null, false)) {
    $isDeveloper = true;
} else {
    $isDeveloper = false;
}
?>
<!-- Left Sidebar --><!--suppress HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget -->
<ul class="nav">
    <li class="nav-item">
        <a class="nav-link" href="main.html"><i class="fa fa-dashboard"></i> Dashboard</a>
    </li>

    <li class="nav-title">
        Activities
    </li>
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-soccer-ball-o"></i> Activities</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="activities/activity.html"><i class="fa fa-percent"></i> Activity</a>
            </li>
        </ul>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="activities/activity/log.html"><i class="fa fa-archive"></i> Activity Log</a>
            </li>
        </ul>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="dashboard/push.html"><i class="fa fa-dashboard"></i> Push</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="dashboard/journey.html"><i class="fa fa-dashboard"></i> Journey</a>
    </li>

    <li class="nav-title">
        Body
    </li>
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-life-ring"></i> Body</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="body/weight.html"><i class="fa fa-heart"></i> Body Weight</a>
            </li>
        </ul>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="body/fat.html"><i class="fa fa-heartbeat"></i> Body Fat</a>
            </li>
        </ul>
        <?php /*if ($isDeveloper) { */ ?><!--
                                <ul class="nav-dropdown-items">
                                    <li class="nav-item">
                                        <a class="nav-link" href="body/goals.html"><i class="fa fa-bullseye"></i> Body Goals <span class="badge badge-danger">WIP</span></a>
                                    </li>
                                </ul>
		                    --><?php /*}*/ ?>
    </li>

    <li class="nav-title">
        Tracking
    </li>
    <li class="nav-item">
        <a class="nav-link" href="food/food.html"><i class="fa fa-cutlery"></i> Food</a>
    </li>
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-street-view"></i> Nomie</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="nomie/nomie.html"><i class="fa fa-dashboard"></i> Dashboard</a>
            </li>
        </ul>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="nomie/trackers.html"><i class="fa fa-check-square-o"></i> Trackers</a>
            </li>
        </ul>
    </li>

    <li class="nav-title">
        Rewards
    </li>
    <li class="nav-item">
        <a class="nav-link" href="rewards/badges.html"><i class="fa fa-trophy"></i> Badges</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="rewards/leaderboard.html"><i class="fa fa-users"></i> Leaderboard</a>
    </li>

    <li class="nav-title">
        Settings
    </li>
    <li class="nav-item nav-dropdown">
        <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-street-view"></i> Account</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="settings/account.html"><i class="fa fa-users"></i> Basic Profile</a>
            </li>
        </ul>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="settings/privacy.html"><i class="fa fa-eye-slash"></i> Privacy</a>
            </li>
        </ul>
        <ul class="nav-dropdown-items">
            <li class="nav-item">
                <a class="nav-link" href="settings/settings.html"><i class="fa fa-gears"></i> Settings</a>
            </li>
        </ul>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="settings/devices.html"><i class="fa fa-database"></i> Devices</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="settings/progress.html"><i class="fa fa-tasks"></i> Sync Progress</a>
    </li>

</ul>
