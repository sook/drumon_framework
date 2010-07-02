<?php
	$config = array();
	
	// Path to root of the site.  /Users/danillos/Developer/www/danillocesar
	define('ROOT', dirname(__FILE__));
	
	
	// URL para ser utilizado no lado do cliente.
	$dir = dirname($_SERVER["SCRIPT_NAME"]);
	if($dir === '/') $dir = '';
	define('APP_URL', "http://".$_SERVER['SERVER_NAME'].$dir);
	
	
	// Constant to insert others views in view.
	define('VIEW', ROOT.'/views/');
	
	
	// Inclui arquivos essencias.
	include(ROOT.'/config/application.php');
	include(ROOT.'/config/enviroments/'.ENV.'.php');
	include(CORE.'/Main.php');
	
?>
