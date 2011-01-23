<?php

	// Caminho para a pasta raiz do projeto
	define('ROOT', dirname(__FILE__));
	
	// Inclui arquivos básicos do Framework
	include(ROOT.'/config/application.php');
	include(ROOT.'/config/enviroments/'.ENV.'.php');
	include(CORE.'/class/event.php');
	
	// Carrega plugins
	$plugins = (PLUGINS === '') ? array() : explode(',',PLUGINS);
	foreach ($plugins as $plugin) {
		require(ROOT.'/vendor/plugins/'.$plugin.'/initializer.php');
	}
	
	// Dispara o evento de inicialização do Framework
	Event::fire('on_init');
	
	// Inclui o arquivo básico do core
	include(CORE.'/bootstrap.php');
	
?>