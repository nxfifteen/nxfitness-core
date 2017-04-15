<?php
	if ( is_array( $_SERVER ) && array_key_exists( "REDIRECT_URL", $_SERVER ) ) {
		$url = str_replace( $App->getConfig( "/admin" ), "", $_SERVER['REDIRECT_URL'] );
	} else {
		$url = "";
	}

	if ($_COOKIE['_nx_fb_usr'] == $App->getNxFit()->getSetting('ownerFuid', NULL, FALSE)) {
		$isDeveloper = true;
	} else {
		$isDeveloper = false;
	}
?>
<!-- Left Sidebar -->
                    <ul class="nav">
                        <li class="nav-item">
                            <a class="nav-link" href="main.html"><i class="icon-speedometer"></i> Dashboard <span class="badge badge-info">NEW</span></a>
                        </li>

                        <li class="nav-title">
                            Activities
                        </li>
                        <li class="nav-item nav-dropdown">
                            <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-signal"></i> Activities <span class="badge badge-info">NEW</span></a>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="activities/activity.html"><i class="fa fa-signal"></i> Activity <span class="badge badge-info">NEW</span></a>
                                </li>
                            </ul>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="activities/activity/log.html"><i class="fa fa-signal"></i> Activity Log <span class="badge badge-info">NEW</span></a>
                                </li>
                            </ul>
                            <?php if ($isDeveloper) { ?>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="activities/activity/goals.html"><i class="fa fa-bullseye"></i> Activity Goals <span class="badge badge-info">NEW</span></a>
                                </li>
                            </ul>
		                    <?php } ?>
                        </li>

                        <li class="nav-title">
                            Body
                        </li>
                        <li class="nav-item nav-dropdown">
                            <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-signal"></i> Body <span class="badge badge-info">NEW</span></a>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="body/body.html"><i class="fa fa-signal"></i> Body <span class="badge badge-info">NEW</span></a>
                                </li>
                            </ul>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="body/weight.html"><i class="fa fa-signal"></i> Body Weight <span class="badge badge-info">NEW</span></a>
                                </li>
                            </ul>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="body/fat.html"><i class="fa fa-signal"></i> Body Fat <span class="badge badge-info">NEW</span></a>
                                </li>
                            </ul>
		                    <?php if ($isDeveloper) { ?>
                                <ul class="nav-dropdown-items">
                                    <li class="nav-item">
                                        <a class="nav-link" href="body/goals.html"><i class="fa fa-bullseye"></i> Body Goals <span class="badge badge-info">NEW</span></a>
                                    </li>
                                </ul>
		                    <?php } ?>
                        </li>

                        <li class="nav-title">
                            Tracking
                        </li>
                        <li class="nav-item nav-dropdown">
                            <a class="nav-link nav-dropdown-toggle" href="#"><i class="fa fa-signal"></i> Food <span class="badge badge-info">NEW</span></a>
                            <ul class="nav-dropdown-items">
                                <li class="nav-item">
                                    <a class="nav-link" href="food/food.html"><i class="fa fa-signal"></i> Food <span class="badge badge-info">NEW</span></a>
                                </li>
                            </ul>
		                    <?php if ($isDeveloper) { ?>
                                <ul class="nav-dropdown-items">
                                    <li class="nav-item">
                                        <a class="nav-link" href="food/goals.html"><i class="fa fa-bullseye"></i> Food Goals <span class="badge badge-info">NEW</span></a>
                                    </li>
                                </ul>
		                    <?php } ?>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="nomie/nomie.html"><i class="icon-speedometer"></i> Nomie <span class="badge badge-info">NEW</span></a>
                        </li>

                        <li class="nav-title">
                            Rewards
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rewards/badges.html"><i class="icon-speedometer"></i> Badges <span class="badge badge-info">NEW</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rewards/leaderboard.html"><i class="icon-speedometer"></i> Leaderboard <span class="badge badge-info">NEW</span></a>
                        </li>

                        <li class="nav-title">
                            Settings
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings/devices.html"><i class="icon-speedometer"></i> Devices <span class="badge badge-info">NEW</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings/feeds.html"><i class="icon-speedometer"></i> Feeds <span class="badge badge-info">NEW</span></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="settings/progress.html"><i class="icon-speedometer"></i> Sync Progress <span class="badge badge-info">NEW</span></a>
                        </li>

                    </ul>
