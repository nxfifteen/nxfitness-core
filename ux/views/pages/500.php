<?php
    if (is_dir(dirname(__FILE__) . "/../../bower_components")) {
        $bowerPath = 'bower_components';
    } else if (is_dir(dirname(__FILE__) . "/../../../bundle/bowser")) {
        $bowerPath = '../bundle/bowser';
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
            <div class="clearfix">
                <h1 class="float-left display-3 mr-4">500</h1>
                <h4 class="pt-3">Houston, we have a problem!</h4>
                <p class="text-muted">The page you are looking for is temporarily unavailable.</p>
            </div>
        </div>
    </div>
</div>

<!--suppress JSUnusedLocalSymbols -->
<script type="application/javascript">
    var fitbitUserId = '<?php echo $_COOKIE['_nx_fb_usr']; ?>';
    <?php echo "var bowerPath = '" . $bowerPath . "';\n"; ?>
</script>

<!-- Bootstrap and necessary plugins -->
<script src="../../<?php echo $bowerPath; ?>/jquery/dist/jquery.min.js"></script>
<script src="../../<?php echo $bowerPath; ?>/tether/dist/js/tether.min.js"></script>
<script src="../../<?php echo $bowerPath; ?>/bootstrap/dist/js/bootstrap.min.js"></script>

</body>

</html>
