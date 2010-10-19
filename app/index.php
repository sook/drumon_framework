<?php

	// Path to root of the site.  /Users/danillos/Developer/www/danillocesar
	define('ROOT', dirname(__FILE__));
	include(ROOT.'/config/application.php');
	
	// Define se está um um subdiretório.
	$real_folder = dirname($_SERVER["SCRIPT_NAME"]);
	$uri = $_SERVER['REQUEST_URI'];
	$uri = explode('/',$_SERVER['REQUEST_URI']);
	$dir = '/'.$uri[1];
	if($dir === '/' || $real_folder != $dir) $dir = '';
	
	// URL para ser utilizado no lado do cliente.
	define('APP_DOMAIN', "http://".$_SERVER['SERVER_NAME'].$dir);
	
	// Constant to insert others views in view.
	define('VIEW', ROOT.'/views/');
	
	// Inclui arquivos essencias.
	include(ROOT.'/config/enviroments/'.ENV.'.php');
	
	// Carrega plugins.
	$plugins = (PLUGINS === '') ? array() : explode(',',PLUGINS);
	foreach ($plugins as $plugin) {
		require(ROOT.'/plugins/'.$plugin.'/initializer.php');
	}
	
	include(CORE.'/Main.php');
?>