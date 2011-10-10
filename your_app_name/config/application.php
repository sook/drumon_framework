<?php

	// Application environment (development|production)
	$app->config['env'] = isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'development';
	
	// Language for your application (pt-BR|en-US|...)
	$app->config['language'] = 'pt-BR';
	
	// Set application charset
	$app->config['charset'] = 'utf-8';
	
	// Application plugin list
	// $app->add_plugins('drumon_model');
	
	// Default Javascript Framework (mootools|jquery)
	$app->config['js_framework'] = 'jquery';
	
	// Application sercret, for protect against CSRF
	$app->config['app_secret'] = '!CHANGE THIS VALUE!';
	
	// Application custom configurations
	// $app->config['key'] = 'value';
	
?>