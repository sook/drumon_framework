<?php
	
	// Domínio de sua aplicação
	define('APP_DOMAIN','http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']));
	
	// Application Paths
	define('STYLESHEETS_PATH', APP_DOMAIN.'/public/stylesheets/');
	define('JAVASCRIPTS_PATH', APP_DOMAIN.'/public/javascripts/');
	define('IMAGES_PATH',      APP_DOMAIN.'/public/images/');
	
	// Configurações do banco de dados
	$app->config['db'] = array(
		'host' => 'localhost',
		'user' => 'root',
		'password' => '',
		'name' => 'database_name',
		'charset' => 'utf8'
	);
	
	// Adiciona plugin de benchmark
	$app->add_plugins('benchmark');
	
?>