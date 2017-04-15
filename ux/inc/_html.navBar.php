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

                    </ul>
