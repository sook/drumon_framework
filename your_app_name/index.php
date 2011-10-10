<?php

	// Application paths
	define('APP_PATH', dirname(__FILE__));
	define('CORE_PATH', APP_PATH . '/vendor/drumon_core');

	// Application class
	include(CORE_PATH . '/class/app.php');
	
	// Run application
	App::run();
	
?>