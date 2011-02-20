<?php

	// Ambiente da aplicação. (development|production)
	$app->config['env'] = isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'development';
	
	// Linguagem da sua apicação. (pt-BR|en-US|...)
	$app->config['language'] = 'pt-BR';
	
	// Helpers carregados automaticamente. (html,date,text,url,image)
	$app->add_helpers(array('html','date','text','url'));
	
	// Plugins utilizados em sua app
	//$app->add_plugins('drumon_model');
	
	// Javascript framework. (mootools|jquery)
	$app->config['js_framework'] = 'jquery';
	
	// Segredo da aplicação para proteção contra CSRF. !ALTERE ESSE VALOR!
	define('APP_SECRET','altere-esse-valor-urgente');
	
	// Configurações personalizadas de sua aplicação
	// $app->config['key'] = 'value';
	
?>