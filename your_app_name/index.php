<?php
	// Caminho para a pasta raiz do projeto
	define('ROOT', dirname(__FILE__));
	define('CORE', ROOT.'/vendor/drumon_core');

	// Classe principal do framework
	include(CORE.'/class/app.php');
	// Roda a aplicação
	App::run();
?>