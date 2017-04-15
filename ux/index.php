<?php
	session_start();

	define('PROJECT_ROOT', dirname(__FILE__));
	define("PATH_ADMIN", dirname(__FILE__) . "/");
	define("PATH_ROOT", dirname(__FILE__) . "/../");

	$_COOKIE['_nx_fb_usr'] = '269VLG';
	$_SESSION['PROJECT_ROOT'] = PROJECT_ROOT;
	$_SESSION['PATH_ADMIN'] = PATH_ADMIN;
	$_SESSION['PATH_ROOT'] = PATH_ROOT;

	require_once(dirname(__FILE__) . "/_class/NxFitAdmin.php");
	$App = new NxFitAdmin($_COOKIE['_nx_fb_usr']);
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <?php require_once ('inc/_html.header.php'); ?>
    </head>

    <body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden">
        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button">&#9776;</button>
            <a class="navbar-brand" href="#"></a>
            <ul class="nav navbar-nav d-md-down-none">
                <li class="nav-item">
                    <a class="nav-link navbar-toggler sidebar-toggler" href="#">&#9776;</a>
                </li>

	            <?php require_once ('inc/_html.topMenu.php'); ?>
            </ul>

	        <?php require_once ('inc/_html.topUserMenu.php'); ?>
        </header>

        <div class="app-body">
            <div class="sidebar">
                <nav class="sidebar-nav">
	                <?php require_once ('inc/_html.navBar.php'); ?>
                </nav>
            </div>

            <!-- Main content -->
            <main class="main">
	            <?php require_once ('inc/_html.breadcrumb.php'); ?>

                <div class="container-fluid">
                    <div id="ui-view"></div>
                </div>
                <!-- /.conainer-fluid -->
            </main>

            <aside class="aside-menu">
	            <?php require_once ('inc/_html.asideMenu.php'); ?>
            </aside>

        </div>

        <footer class="app-footer">
            <a href="https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core">NxFITNESS Core</a> &copy; 2017 Stuart McCulloch Anderson.
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
