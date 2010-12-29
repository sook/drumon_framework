<?php
/**
 * *********** BOOTSTRAP ************
 */
	
	$route = array();
	$route['404'] = '404.html'; // rota padrão do erro 404
	$route['401'] = '401.html'; // rota padrão do erro 401
	
	include(CORE.'/class/drumon.php');
	include(ROOT.'/config/routes.php');
	include(CORE.'/class/request_handler.php');
	include(CORE.'/class/helper.php');
	include(CORE.'/class/template.php');
	include(CORE.'/class/controller.php');
	include(ROOT.'/app/controllers/app_controller.php');

	/**
	 * Inicia o sistema de roteamento.
	 */
	$request = new RequestHandler($route);

	// Protege a aplicação contra CSFR.
	Drumon::fire_csrf_protection($request);
	
	// Se requisição for válida.
	if($request->valid) {
		$html = Drumon::execute_controller($request);
	} else {
		header("HTTP/1.0 404 Not Found");
		if (is_array($route['404'])) {
			$request->set_controller_name($route['404'][0]);
			$request->set_action_name($route['404'][1]);
			
			$html = Drumon::execute_controller($request);
		}else{
			$html = file_get_contents(ROOT.'/public/'.$route['404']);
		}
	}
	
	// Imprime o Html.
	Event::fire('before_render',array('content' => &$html));
	echo $html;
	Event::fire('after_render');
	
?>