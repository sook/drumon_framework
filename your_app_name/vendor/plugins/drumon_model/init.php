<?php

	include 'class/connection.php';
	include 'class/drumon_model.php';
	include 'class/behavior.php';
	
	define('ORM_PATH', PLUGINS_PATH.'/drumon_model');
	
	$con = Connection::get_instance();
	$con->connect($app->config['db']);
	
	$app->add_helpers('paginate', PLUGINS_PATH.'/drumon_model/helpers');
	
	// Adiciona internacionalização na paginação
	$app->translations_cache['plugin:drumon_model:pagination'] = include(PLUGINS_PATH.'/drumon_model/locales/'.LANGUAGE.'/pagination.php');
	
	
	function auto_load_model($model_name) {
		$file = ROOT.'/app/models/'.App::to_underscore($model_name).'.php';
		if (file_exists($file)) {
			require $file;
		}
	}
	
	spl_autoload_register("auto_load_model");
	
?>