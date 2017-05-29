<?php
/*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

/** @noinspection PhpUndefinedMethodInspection */
$userProfile = $App->getUserProfile();

if ( array_key_exists( "HeaderNotificationBar",
        $_SESSION ) && is_array( $_SESSION[ 'HeaderNotificationBar' ] ) && count( $_SESSION[ 'HeaderNotificationBar' ] ) > 0
) {
    $HeaderNotificationBar = $_SESSION[ 'HeaderNotificationBar' ];

    nxr( 1, "* HeaderNotificationBar taken from session" );
} else {

    $HeaderNotificationBar = [];
    /** @noinspection PhpUndefinedMethodInspection */
    if ( ! $App->getNxFit()->isUserOAuthAuthorised( $_COOKIE[ '_nx_fb_usr' ] ) ) {
        /** @noinspection PhpUndefinedMethodInspection, HtmlUnknownTarget */
        array_push( $HeaderNotificationBar, $App->getThemeWidgets( "HeaderNotificationBar", [
            "msg"     => "<a href=\"../authorise\">Fitbit OAuth Setup Required</a>",
            "urgency" => "danger",
            "icon"    => "bug"
        ] ) );
    } else {
        if ( isset( $userProfile[ 'cooldown' ] ) && strtotime( $userProfile[ 'cooldown' ] ) > strtotime( date( "Y-m-d H:i:s" ) ) ) {
            /** @noinspection PhpUndefinedMethodInspection */
            array_push( $HeaderNotificationBar, $App->getThemeWidgets( "HeaderNotificationBar", [
                "msg"     => "Sync Status - Fitbit API limit reached. ",
                "urgency" => "danger",
                "icon"    => "dashboard"
            ] ) );
        }

        if ( count( $HeaderNotificationBar ) > 0 ) {
            $_SESSION[ 'HeaderNotificationBar' ] = $HeaderNotificationBar;
        }
    }
}
?>

<!--suppress HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget, HtmlUnknownTarget -->
<ul class="nav navbar-nav ml-auto">

    <li class="nav-item d-md-down-none">
        <a class="nav-link" href="#"><i class="icon-bell"></i><span class="badge badge-pill badge-danger"><?php echo count( $HeaderNotificationBar ); ?></span></a>
    </li>
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <img src="<?php echo $userProfile[ 'avatar' ]; ?>" class="img-avatar" alt="<?php echo $userProfile[ 'name' ]; ?>"> <span class="d-md-down-none"><?php echo $userProfile[ 'name' ]; ?></span> </a>
        <div class="dropdown-menu dropdown-menu-right">

            <div class="dropdown-header text-center">
                <strong>Account</strong>
            </div>

            <a class="dropdown-item" href="#"><i class="fa fa-bell-o"></i> Updates<span class="badge badge-info"><?php echo count( $HeaderNotificationBar ); ?></span></a>

            <div class="dropdown-header text-center">
                <strong>Settings</strong>
            </div>

            <a class="dropdown-item" href="#"><i class="fa fa-user"></i> Profile</a> <a class="dropdown-item" href="#"><i class="fa fa-wrench"></i> Settings</a> <a class="dropdown-item" href="refresh"><i class="fa fa-refresh"></i>
                Refresh</a>
            <div class="divider"></div>
            <a class="dropdown-item" href="./views/pages/logout"><i class="fa fa-lock"></i> Logout</a>
        </div>
    </li>
    <li class="nav-item d-md-down-none">
        <a class="nav-link navbar-toggler aside-menu-toggler" href="#">&#9776;</a>
    </li>

</ul>
