<?php
	// Caminho para a pasta do core do Drumon Framework
	define('CORE','vendor/drumon_core');
	
	// Domínio de sua aplicação
	define('APP_DOMAIN','http://www.yourdomain.com');
	
	// Configurações do banco de dados
	define('DB_HOST',			'localhost');
	define('DB_USER',			'root');
	define('DB_PASSWORD', '');
	define('DB_NAME',			'database_name');
	define('CHARSET',			'utf8');
	
	// Application Paths
	define('STYLESHEETS_PATH', APP_DOMAIN.'/public/stylesheets/');
	define('JAVASCRIPTS_PATH', APP_DOMAIN.'/public/javascripts/');
	define('IMAGES_PATH',      APP_DOMAIN.'/public/images/');
	
	// Mostra tempos do benchmark
	define('BENCHMARK',false);

?>