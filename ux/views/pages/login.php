<?php
    /*******************************************************************************
 * This file is part of NxFIFTEEN Fitness Core.
 *
 * Copyright (c) 2017. Stuart McCulloch Anderson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 ******************************************************************************/

    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header("X-Clacks-Overhead: GNU Terry Pratchett");

    session_start();

    $config = array();
    if (!array_key_exists("admin_config", $_SESSION)) {
        require_once("../../config.inc.php");
    } else {
        $config = $_SESSION['admin_config'];
    }
?>
<!DOCTYPE html><!--suppress HtmlUnknownTarget, HtmlUnknownTarget -->
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
        <div class="col-md-12">
            <div class="card-group mb-0">
                <div class="card p-4">
                    <div class="card-block">
                        <h1>Login</h1>
                        <p class="text-muted">Sign In to your account</p>
                        <form action="<?php echo $config['http/admin']; ?>/login/redirect" method="post">
                            <div class="input-group mb-3">
                                    <span class="input-group-addon"><i class="icon-user"></i>
                                    </span>
                                <input type="text" class="form-control" placeholder="Username" name="fuid" autofocus>
                            </div>
                            <div class="input-group mb-4">
                                    <span class="input-group-addon"><i class="icon-lock"></i>
                                    </span>
                                <input type="password" class="form-control" placeholder="Password" name="password">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <input type="checkbox" name="remember" value="remember-me" title="remember me">
                                    Remember me
                                </div>
                                <div class="col-6 text-right">
                                    <button class="btn btn-primary px-4" type="submit">Login</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card card-inverse card-primary py-5 d-md-down-none" style="width:22%">
                    <div class="card-block text-center">
                        <div>
                            <h2>Sign up</h2>
                            <p>Sign in to your Fitbit account to start creating a Core profile.</p>
                            <a href="<?php echo $config['http/']; ?>register" class="btn btn-primary active mt-3">Register
                                                                                                                  Now!</a>
                        </div>
                    </div>
                </div>
                <div class="card card-inverse card-info py-5 d-md-down-none" style="width:22%">
                    <div class="card-block text-center">
                        <div>
                            <h2>Beta</h2>
                            <p>
                                Currently Core is in active Beta, you will have to be on my Fitbit friends list for your
                                profile to work. You can request access to the beta, first read the FAQ bellow. </p>
                            <a class="btn btn-info active mt-3" href="https://nxfifteen.me.uk/gitlab/nx-fitness/nxfitness-core/wikis/beta-access-request" target="_blank">BETA
                                                                                                                                                                          Access
                                                                                                                                                                          FAQ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--suppress JSUnusedLocalSymbols -->
<script type="application/javascript">
    var fitbitUserId = '<?php echo $_COOKIE['_nx_fb_usr']; ?>';
</script>

<!-- Bootstrap and necessary plugins -->
<script src="../../../bundle/bower/jquery/dist/jquery.min.js"></script>
<script src="../../../bundle/bower/tether/dist/js/tether.min.js"></script>
<script src="../../../bundle/bower/bootstrap/dist/js/bootstrap.min.js"></script>

</body>

</html>
