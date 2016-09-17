<?php
	parse_str(implode('&', array_slice($argv, 1)), $argv);
	foreach ($argv as $key => $value) {
		$key = str_ireplace("--", "", $key);
		$_GET[ $key ] = $value;
	}

    $test = array("269VLG");

    require_once(dirname(__FILE__) . "/inc/upgrade.php");
    $dataReturnClass = new Upgrade($test[0]);

	echo "Upgrading from " . $dataReturnClass->getInstallVersion() . " to " . $dataReturnClass->getInstallingVersion() . ". ";
	echo $dataReturnClass->getNumUpdates() . " updates outstanding\n";

	if ($dataReturnClass->getNumUpdates() > 0) {
		$dataReturnClass->runUpdates();
	}
