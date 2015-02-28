<?php
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');

    if (array_key_exists("user", $_GET) && array_key_exists("data", $_GET)) {
        if (is_writable('cache') && (!array_key_exists("cache", $_GET) || $_GET['cache'] != "false")) {
            // cache files are created like cache/abcdef123456...
            $cacheFile = 'cache' . DIRECTORY_SEPARATOR . md5($_SERVER['REQUEST_URI']);

            if (file_exists($cacheFile)) {
                $fh = fopen($cacheFile, 'r');
                $cacheTime = trim(fgets($fh));

                // if data was cached recently, return cached data
                if ($cacheTime > strtotime('-15 minutes')) {
                    echo fread($fh, filesize($cacheFile));
                    return true;
                }

                // else delete cache file
                fclose($fh);
                unlink($cacheFile);
            }

            $json = query_api();
            echo $json;

            $fh = fopen($cacheFile, 'w');
            fwrite($fh, time() . "\n");
            fwrite($fh, $json);
            fclose($fh);
        } else {
            echo query_api();
        }
    } elseif (!array_key_exists("user", $_GET)) {
        echo json_error(100);
    } elseif (!array_key_exists("data", $_GET)) {
        echo json_error(102);
    }

    function query_api() {
        require_once(dirname(__FILE__) . "/inc/dataReturn.php");
        $dataReturnClass = new dataReturn($_GET['user']);
        if ($dataReturnClass->isUser()) {
            $json = json_encode($dataReturnClass->returnUserRecords($_GET));
            //echo "<pre>";
            //print_r($dataReturnClass->returnUserRecords($_GET));
            //echo "</pre>";
            return $json;
        } else {
            echo json_error(101);
            return "";
        }
    }

    function json_error($errNumber) {
        $errMessage = "";
        switch ($errNumber) {
            case "100":
                $errMessage = "You must specifiy a user";
                break;
            case "101":
                $errMessage = "You must specifiy a valid user";
                break;
            case "102":
                $errMessage = "You havent stated what to return";
                break;
        }


        return json_encode(array("error" => "true", "code" => $errNumber, "msg" => $errMessage));
    }
