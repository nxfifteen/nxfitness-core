<?php

    if (array_key_exists("user", $_GET) && array_key_exists("data", $_GET)) {
        require_once(dirname(__FILE__) . "/inc/dataReturn.php");
        $dataReturnClass = new dataReturn($_GET['user']);
        if ($dataReturnClass->isUser()) {
            echo json_encode($dataReturnClass->returnUserRecords($_GET));
        } else {
            echo json_error(101);
        }
    } elseif (!array_key_exists("user", $_GET)) {
        echo json_error(100);
    } elseif (!array_key_exists("data", $_GET)) {
        echo json_error(102);
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
