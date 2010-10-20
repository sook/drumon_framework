<?php
	// Caminho para a pasta do core do Drumon Framework
	define('CORE', '../drumon_core');
	
	
	// Domínio de onde sua aplicação
	define('APP_DOMAIN', 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']));
	
	
	// Configurações do banco de dados
	define('DB_HOST',			'localhost');
	define('DB_USER',			'root');
	define('DB_PASSWORD', '');
	define('DB_NAME',			'');
	define('CHARSET',			'utf8');
	
	
	
	
	// Application Paths
	define('STYLESHEETS_PATH', APP_DOMAIN.'/public/css/');
	define('JAVASCRIPTS_PATH',APP_DOMAIN.'/public/javascripts/');
	define('IMAGES_PATH',APP_DOMAIN.'/public/images/');
	define('MODULES_PATH', APP_DOMAIN.'/public/modules/');
	
	
	// Mostra tempos do benchmark
	define('BENCHMARK',true);

?>
