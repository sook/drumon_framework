<?
/**
 * *********** BOOT ************
 */

	/**
	 * Carrega todos os arquivos necessários.
	 */
	if(LANGUAGE) {
		include(ROOT.'/config/locales/'.LANGUAGE.'.php');
	} else {
		$locale = array();
	}
	
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


	/**
	 * Inicia o controlador.
	 */
	if($request->valid){
		$controller_name = $request->controller_name."Controller";
		include(ROOT.'/app/controllers/'.Drumon::to_underscore($controller_name).'.php');
		$controller = new $controller_name($request,$locale);
		$controller->execute($request->action_name);
	}else{
		header("HTTP/1.0 404 Not Found");
		if(ERROR_404 === '') {
			echo 'Not Found.';
			die();
		}
	include(ROOT.'/public/'.ERROR_404);
	die();
}
?>
