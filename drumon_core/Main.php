<?
/**
 * *********** BOOT ************
 */

	/**
	 * Carrega todos os arquivos necessários.
	 */
	if(LANGUAGE) {
		include(ROOT.'/config/i18n/'.LANGUAGE.'.php');
	} else {
		$i18n = array();
	}

	include(ROOT.'/config/routes.php');
	include(CORE.'/class/RequestHandler.php');
	include(CORE.'/class/Helper.php');
	include(CORE.'/class/Template.php');
	include(CORE.'/class/Controller.php');
	include(ROOT.'/app/controllers/AppController.php');

	/**
	 * Inicia o sistema de roteamento.
	 */
	$request = new RequestHandler($route);


	/**
	 * Inicia o controlador.
	 */
	if($request->valid){
		$controller_name = $request->controller_name."Controller";
		include(ROOT.'/app/controllers/'.$controller_name.'.php');
		$controller = new $controller_name($request,$i18n);
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
