<?php

	// Caminho para a pasta raiz do projeto
	define('ROOT', dirname(__FILE__));
	include(ROOT.'/config/application.php');
	
	// Incluí arquivos essências
	include(ROOT.'/config/enviroments/'.ENV.'.php');
	include(CORE.'/class/event.php');
	
	// Carrega plugins
	$plugins = (PLUGINS === '') ? array() : explode(',',PLUGINS);
	foreach ($plugins as $plugin) {
		require(ROOT.'/plugins/'.$plugin.'/initializer.php');
	}
	Event::fire('on_init');
	include(CORE.'/bootstrap.php');
?>