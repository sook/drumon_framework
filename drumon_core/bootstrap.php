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
	
	/**
	 * Inicia o controlador.
	 */
	if($request->valid) {
		$path = ROOT.'/app/controllers/';
		$class_parts = explode('_',$request->controller_name);
		$file_name = $request->controller_name.'Controller';
		$namespaces = null;
		$last_name = $request->controller_name;

		// Monta o namespace
		if(count($class_parts) > 1) {
			$last_name = array_pop($class_parts);
			$namespaces = implode('/',$class_parts);
			$path .= Drumon::to_underscore($namespaces).'/';
			$file_name = $last_name.'Controller';
		}

		// Inclui o controlador.
		include($path.Drumon::to_underscore($file_name).'.php');
		$class_name = $request->controller_name.'Controller';
		
		// Inicia o controlador e chama a ação.
		$controller = new $class_name($request,$namespaces,$last_name);
		$html = $controller->execute($request->action_name);

		// Dispara os eventos e imprimi o html.
		Event::fire('before_render',array('content' => &$html));
		echo $html;
		Event::fire('after_render',array('content' => &$html));
		
	}else{
		header("HTTP/1.0 404 Not Found");
		
		if (is_array($route['404'])) {
			$request->set_controller_name($route['404'][0]);
			$request->set_action_name($route['404'][1]);
			
			$controller_name = $request->controller_name."Controller";
			include(ROOT.'/app/controllers/'.Drumon::to_underscore($controller_name).'.php');
			$controller = new $controller_name($request,$locale);
			$controller->execute($request->action_name);
			
		}else{
			include(ROOT.'/public/'.$route['404']);
			die();
		}
}
?>