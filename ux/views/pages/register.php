<?php
	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	session_start();

	$config = array();
	if ( !array_key_exists("admin_config", $_SESSION) ) {
		require_once("../../config.inc.php");
	} else {
		$config = $_SESSION['admin_config'];
	}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="NxFIFTEEN || NxFITNESS">
    <meta name="author" content="Stuart McCulloch Anderson">
    <meta name="keyword" content="">
    <link rel="shortcut icon" href="../../img/favicon.png">

    <title>NxFIFTEEN Fitness & Health</title>

    <!-- Icons -->
    <!--<link href="css/nomie.css" rel="stylesheet">-->
    <link href="../../css/font-awesome.min.css" rel="stylesheet">
    <link href="../../css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="../../css/style.css" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="../../img/apple-touch-icon.png?v=A00aBgaPR5">
    <link rel="icon" type="image/png" href="../../img/favicon-32x32.png?v=A00aBgaPR5" sizes="32x32">
    <link rel="icon" type="image/png" href="../../img/favicon-194x194.png?v=A00aBgaPR5" sizes="194x194">
    <link rel="icon" type="image/png" href="../../img/android-chrome-192x192.png?v=A00aBgaPR5" sizes="192x192">
    <link rel="icon" type="image/png" href="../../img/favicon-16x16.png?v=A00aBgaPR5" sizes="16x16">
    <link rel="manifest" href="../../img/manifest.json?v=A00aBgaPR5">
    <link rel="mask-icon" href="../../img/safari-pinned-tab.svg?v=A00aBgaPR5" color="#b91d47">
    <link rel="shortcut icon" href="../../img/favicon.ico?v=A00aBgaPR6">
    <meta name="apple-mobile-web-app-title" content="NxFITNESS">
    <meta name="application-name" content="NxFITNESS">
    <meta name="msapplication-TileColor" content="#db1d1d">
    <meta name="msapplication-TileImage" content="img/mstile-144x144.png?v=A00aBgaPR5">
    <meta name="theme-color" content="#ffffff">

    <!-- Icons -->
    <link href="../../css/font-awesome.min.css" rel="stylesheet">
    <link href="../../css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="../../css/style.css" rel="stylesheet">

</head>

<body class="app flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mx-4">
                    <div class="card-block p-4">
                        <form class="form-signin" action="<?php echo $_SESSION['admin_config']['http/admin']; ?>/login/redirect" method="post">
                            <h1>Register</h1>
                            <p class="text-muted">Create your account</p>
                            <div class="input-group mb-3">
                                <span class="input-group-addon"><i class="icon-user"></i></span>
                                <input type="text" class="form-control" name="fuid" <?php if (isset($_GET['usr'])) { echo " value=\"".$_GET['usr']."\""; } else { echo " placeholder=\"User Name\""; } ?>>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-addon">@</span>
                                <input type="text" class="form-control" <?php if (isset($_GET['eml'])) { echo " value=\"".$_GET['eml']."\""; } else { echo " placeholder=\"Email\""; } ?> name="email" autofocus>
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-addon"><i class="icon-lock"></i></span>
                                <input type="password" class="form-control" placeholder="Password" name="password">
                            </div>

                            <div class="input-group mb-4">
                                <span class="input-group-addon"><i class="icon-lock"></i></span>
                                <input type="password" class="form-control" placeholder="Repeat password" name="password2">
                            </div>

                            <button type="submit" class="btn btn-block btn-success">Create Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and necessary plugins -->
    <script src="../../bower_components/jquery/dist/jquery.min.js"></script>
    <script src="../../bower_components/tether/dist/js/tether.min.js"></script>
    <script src="../../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>

</html>
