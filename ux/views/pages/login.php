<?php
	header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s') . ' GMT');
	header('Cache-Control: no-store, no-cache, must-revalidate');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="CoreUI Bootstrap 4 Admin Template">
    <meta name="author" content="Lukasz Holeczek">
    <meta name="keyword" content="CoreUI Bootstrap 4 Admin Template">
    <!-- <link rel="shortcut icon" href="assets/ico/favicon.png"> -->

    <title>CoreUI Bootstrap 4 Admin Template</title>

    <!-- Icons -->
    <link href="../../css/font-awesome.min.css" rel="stylesheet">
    <link href="../../css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="../../css/style.css" rel="stylesheet">

</head>

<body class="app flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card-group mb-0">
                    <div class="card p-4">
                        <div class="card-block">
                            <h1>Login</h1>
                            <p class="text-muted">Sign In to your account</p>
                            <form action="<?php echo $_SESSION['admin_config']['http/admin']; ?>/login/redirect" method="post">
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
                                    <div class="col-12 text-right">
                                        <button class="btn btn-primary px-4" type="submit">Login</button>
                                    </div>
                                    <!--<div class="col-6">-->
                                    <!--    <button type="button" class="btn btn-link px-0">Forgot password?</button>-->
                                    <!--</div>-->
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card card-inverse card-primary py-5 d-md-down-none" style="width:44%">
                        <div class="card-block text-center">
                            <div>
                                <h2>Sign up</h2>
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                <a href="<?php echo $_SESSION['admin_config']['http/']; ?>register" class="btn btn-primary active mt-3">Register Now!</a>
                            </div>
                        </div>
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
