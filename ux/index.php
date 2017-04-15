<?php
	header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
	header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
	header( 'Cache-Control: no-store, no-cache, must-revalidate' );
	header( 'Cache-Control: post-check=0, pre-check=0', FALSE );
	header( 'Pragma: no-cache' );

	if ( ! function_exists( "nxr" ) || ! function_exists( "nxr_destroy_session" ) ) {
		require_once( dirname( __FILE__ ) . "/../inc/functions.php" );
	}

	define( 'PROJECT_ROOT', dirname( __FILE__ ) );
	define( "PATH_ADMIN", dirname( __FILE__ ) . "/" );
	define( "PATH_ROOT", dirname( __FILE__ ) . "/../" );

	// start the session
	session_start();
	if ( ! array_key_exists( "timeout", $_SESSION ) || ! is_numeric( $_SESSION['timeout'] ) ) {
		$_SESSION['timeout'] = time() + 60 * 5;
	} else if ( $_SESSION['timeout'] < time() ) {
		nxr_destroy_session();
		header( "Location: ./" );
	}

	if ( ! array_key_exists( "admin_config", $_SESSION ) || ! is_array( $_SESSION['admin_config'] ) || count( $_SESSION['admin_config'] ) == 0 ) {
		require_once( PATH_ADMIN . "/config.inc.php" );
		if ( isset( $config ) ) {
			$_SESSION['admin_config'] = $config;
		}
	}

	if ( is_array( $_GET ) && array_key_exists( "err", $_GET ) ) {

		//nxr(__LINE__, TRUE, TRUE, FALSE);
		if ( ! isset( $config ) ) {
			require_once( PATH_ADMIN . "/config.inc.php" );
		}
		nxr_destroy_session();
		header( "Location: ./views/pages/500.html" );
	} elseif ( array_key_exists( "REDIRECT_URL", $_SERVER ) && $_SERVER['REDIRECT_URL'] == $_SESSION['admin_config']['/admin'] . "/login" ) {

		//nxr(__LINE__, TRUE, TRUE, FALSE);
		header( "Location: ./views/pages/login.php" );
	} elseif ( array_key_exists( "REDIRECT_URL", $_SERVER ) && $_SERVER['REDIRECT_URL'] == $_SESSION['admin_config']['/admin'] . "/views/refresh" ) {

		//nxr(__LINE__, TRUE, TRUE, FALSE);
		nxr_destroy_session();
		header( "Location: ./" );
	} elseif ( array_key_exists( "REDIRECT_URL", $_SERVER ) && $_SERVER['REDIRECT_URL'] == $_SESSION['admin_config']['/admin'] . "/login/redirect" ) {

		//nxr(__LINE__, TRUE, TRUE, FALSE);
		require_once( "_class/UserLogin.php" );
	} elseif ( array_key_exists( "REDIRECT_URL", $_SERVER ) && $_SERVER['REDIRECT_URL'] == $_SESSION['admin_config']['/admin'] . "/views/register" ) {

		//nxr(__LINE__, TRUE, TRUE, FALSE);
		header( "Location: ./views/pages/register.php" );
	} elseif ( array_key_exists( "REDIRECT_URL", $_SERVER ) && $_SERVER['REDIRECT_URL'] == $_SESSION['admin_config']['/admin'] . "/views/logout" ) {

		//nxr(__LINE__, TRUE, TRUE, FALSE);
		setcookie( '_nx_fb_key', '', time() - 60 * 60 * 24 * 365, '/', $_SERVER['SERVER_NAME'] );
		setcookie( '_nx_fb_usr', '', time() - 60 * 60 * 24 * 365, '/', $_SERVER['SERVER_NAME'] );

		$path = $_SESSION['admin_config']['http/admin'];
		//nxr($path . '/views/pages/login.php', TRUE, TRUE, FALSE);

		nxr_destroy_session();

		header( "Location: " . $path . '/views/pages/login.php' );
	} elseif ( ! is_array( $_COOKIE ) || ! array_key_exists( "_nx_fb_usr", $_COOKIE ) ) {

		//nxr(__LINE__, TRUE, TRUE, FALSE);
		header( "Location: ./views/pages/login.php" );
	} else {

		//nxr(__LINE__, TRUE, TRUE, FALSE);
		//nxr(print_r($_SERVER, TRUE), TRUE, TRUE, FALSE);
		$_SESSION['PROJECT_ROOT'] = PROJECT_ROOT;
		$_SESSION['PATH_ADMIN']   = PATH_ADMIN;
		$_SESSION['PATH_ROOT']    = PATH_ROOT;

		require_once( dirname( __FILE__ ) . "/_class/NxFitAdmin.php" );
		$App = new NxFitAdmin( $_COOKIE['_nx_fb_usr'] );
		?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
			<?php require_once( 'inc/_html.header.php' ); ?>
        </head>

        <body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button">&#9776;</button>
            <a class="navbar-brand" href="#"></a>
            <ul class="nav navbar-nav d-md-down-none">
                <li class="nav-item">
                    <a class="nav-link navbar-toggler sidebar-toggler" href="#">&#9776;</a>
                </li>

				<?php require_once( 'inc/_html.topMenu.php' ); ?>
            </ul>

			<?php require_once( 'inc/_html.topUserMenu.php' ); ?>
        </header>

        <div class="app-body">
            <div class="sidebar">
                <nav class="sidebar-nav">
					<?php require_once( 'inc/_html.navBar.php' ); ?>
                </nav>
            </div>

            <!-- Main content -->
            <main class="main">
				<?php require_once( 'inc/_html.breadcrumb.php' ); ?>

                <div class="container-fluid">
                    <div id="ui-view"></div>
                </div>
                <!-- /.conainer-fluid -->
            </main>

            <aside class="aside-menu">
				<?php require_once( 'inc/_html.asideMenu.php' ); ?>
            </aside>

        </div>

        <footer class="app-footer">
            <a href="https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core">NxFITNESS Core</a> &copy; 2017 Stuart
                                                                                                  McCulloch Anderson.
            <span class="float-right">Powered by <a href="http://coreui.io">CoreUI</a></span>
        </footer>

        <script type="application/javascript">
            var fitbitUserId = '<?php echo $_COOKIE['_nx_fb_usr']; ?>';
        </script>

        <!-- Bootstrap and necessary plugins -->
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <script src="bower_components/tether/dist/js/tether.min.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="bower_components/pace/pace.min.js"></script>
        <script src="bower_components/raven-js/dist/raven.min.js"></script>

        <!-- GenesisUI main scripts -->
        <script>Raven.config('https://80a480ea986d4ee993ac89a54a0d1f0e@sentry.io/156527').install();</script>
        <script src="js/app.js"></script>

        </body>

        </html>
	<?php
	}
?>