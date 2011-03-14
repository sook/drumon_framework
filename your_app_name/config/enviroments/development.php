<?php
	
	// Domínio de sua aplicação
	//$app->config['app_domain'] = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']);
	
	// Application Paths
	//$app->config['stylesheets_path'] = $app->config['app_domain'].'/public/stylesheets/';
	//$app->config['javascripts_path'] = $app->config['app_domain'].'/public/javascripts/';
	//$app->config['images_path'] = $app->config['app_domain'].'/public/images/';
	
	// Configurações do banco de dados
	$app->config['db'] = array(
		'host' => 'localhost',
		'user' => 'root',
		'password' => '',
		'database' => '',
		'charset' => 'utf8'
	);
	
	// Adiciona plugin de benchmark
	$app->add_plugins('benchmark');
	
?>