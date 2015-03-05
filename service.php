<?php
    header('HTTP/1.0 204 No Content');

    $data = json_decode(file_get_contents('php://input'));
    $fh      = fopen("/home/nxad/logs/fitbit.upload.txt", "a");
    fwrite($fh, "************\nNew API request:\n" . print_r($data, true) . "\n");