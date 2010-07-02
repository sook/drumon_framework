<?
	/** Ambientes da aplicação
	 * development - Usado durante desenvolvimento da sua applicação.
	 * production - Usado quando sua aplicação está em produção em um servidor.
	 */
	define('ENV','development');

	// Application Paths
	define('CSS_PATH', APP_URL.'/public/css/');
	define('JAVASCRIPTS_PATH',APP_URL.'/public/javascripts/');
	define('IMAGES_PATH',APP_URL.'/public/images/');
	define('MODULES_PATH', APP_URL.'/public/modules/');
	
	
	// Define a linguagem da aplicação
	$config['language'] = 'pt-br';
	
	// Define os helpers do core padrões
	// $config['default_helpers'] = array('Html','Date','Text','Image','Paginate');
	$config['default_helpers'] = array('Html','Date','Text');
	
	
	// Página de erro 404. 
	$config['404'] = '404.html';
?>