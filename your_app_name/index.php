<?php
	// Caminho para a pasta raiz do projeto
	define('APP_PATH', dirname(__FILE__));
	define('CORE_PATH', APP_PATH.'/vendor/drumon_core');

	// Classe principal do framework
	include(CORE_PATH.'/class/app.php');
	// Roda a aplicação
	App::run();
?>