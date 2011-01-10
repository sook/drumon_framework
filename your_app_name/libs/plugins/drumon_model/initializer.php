<?php

	// Caminho para os módulos
	if (ENV === 'production') {
		define('MODULES_PATH', APP_DOMAIN.'/public/modules/');
	}else{
		define('MODULES_PATH', APP_DOMAIN.'/public/modules/');
	}
	
	include('class/drumon_model.php');
	
?>