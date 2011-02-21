<?php
	/**
	 * *********** BOOTSTRAP ************
	 */
	
	// Caminho para a pasta raiz do projeto
	define('ROOT', dirname(__FILE__));
	define('CORE', ROOT.'/vendor/drumon_core');
	
	// Inclui arquivos básicos do Framework
	include(CORE.'/class/app.php');
	include(CORE.'/class/request_handler.php');
	include(CORE.'/class/helper.php');
	include(CORE.'/class/template.php');
	include(CORE.'/class/controller.php');
	
	// Obtem a instancia da aplicação
	$app = App::get_instance();
	
	// Configurações padrões do framework
	$app->config['app_domain']       = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']);
	$app->config['stylesheets_path'] = $app->config['app_domain'].'/public/stylesheets/';
	$app->config['javascripts_path'] = $app->config['app_domain'].'/public/javascripts/';
	$app->config['images_path']      = $app->config['app_domain'].'/public/images/';
	
	
	$route = array();
	$route['404'] = '404.html'; // rota padrão do erro 404
	$route['401'] = '401.html'; // rota padrão do erro 401
	
	include(ROOT.'/config/routes.php');
	include(ROOT.'/config/application.php');
	include(ROOT.'/config/enviroments/'.$app->config['env'].'.php');
	
	// Seta as constantes mais utilizadas
	define('APP_DOMAIN',       $app->config['app_domain']);
	define('STYLESHEETS_PATH', $app->config['stylesheets_path']);
	define('JAVASCRIPTS_PATH', $app->config['javascripts_path']);
	define('IMAGES_PATH',      $app->config['images_path']);
	define('APP_SECRET',       $app->config['app_secret']);
	
	// Carrega plugins
	foreach ($app->plugins as $plugin) {
		require_once(ROOT.'/vendor/plugins/'.$plugin.'/init.php');
	}
	
	include(ROOT.'/app/controllers/app_controller.php');
	
	// Dispara o evento de inicialização do Framework
	$app->fire_event('on_init');
	
	
	/**
	 * Inicia o sistema de roteamento.
	 */
	$request = new RequestHandler($route);
	
	// Se requisição for válida.
	if($request->valid()) {
		define('REQUEST_TOKEN', App::create_request_token());
		// Protege a aplicação contra CSFR.
		if(App::block_csrf_protection($request)) {
			header("HTTP/1.0 401 Unauthorized");
			include(ROOT.'/public/'.$request->routes['401']);
			die(); // remover o die (hora de criar o response?)
		}
		$html = App::execute_controller($app, $request);
	} else {
		header("HTTP/1.0 404 Not Found");
		if (is_array($route['404'])) {
			$request->controller_name = $route['404'][0];
			$request->action_name = $route['404'][1];
			
			$html = App::execute_controller($app, $request);
		}else{
			$html = file_get_contents(ROOT.'/public/'.$route['404']);
		}
	}
	
	
	// Imprime o Html.
	$app->fire_event('before_render', array('content' => &$html));
	echo $html;
	$app->fire_event('after_render');
	
?>