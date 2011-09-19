<?php

	// Ambiente da aplicação. (development|production)
	$app->config['env'] = isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'development';
	
	// Linguagem da sua apicação. (pt-BR|en-US|...)
	$app->config['language'] = 'pt-BR';
	
	// Set application charset
	$app->config['charset'] = 'utf-8';
	
	// Plugins utilizados em sua aplicação
	$app->add_plugins('drumon_model');
	
	// Javascript framework. (mootools|jquery)
	$app->config['js_framework'] = 'jquery';
	
	// Segredo da aplicação para proteção contra CSRF. !ALTERE ESSE VALOR!
	$app->config['app_secret'] = 'altere-esse-valor-urgente';
	
	// Configurações personalizadas de sua aplicação
	// $app->config['key'] = 'value';
	
?>