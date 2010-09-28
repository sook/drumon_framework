<?
	// Inicio Benchmark
	if(BENCHMARK){
		include(CORE.'/class/Benchmark.php');
		Benchmark::start('Load Time');
	}

	/**
	 * *********** BOOT ************
	 */
	
	/**
	 * Carrega todos os arquivos necessários.
	 */
	if(LANGUAGE) {
		include(ROOT.'/config/i18n/'.LANGUAGE.'.php');
		if(LANGUAGE === 'pt-BR') {
			setlocale(LC_ALL, 'portuguese', 'pt_BR', 'pt_br','pt-BR','pt-br', 'ptb_BRA','ptb','bra','portuguese-brazil','brazil','pt_BR.utf-8','br','pt_BR.iso-8859-1');
		}
	} else {
		$i18n = array();
	}

	include(ROOT.'/config/routes.php');
	include(CORE.'/class/RequestHandler.php');
	include(CORE.'/class/Helper.php');
	include(CORE.'/class/Template.php');
	include(CORE.'/class/DrumonModel.php'); // TODO: Desacoplar sem perder perfomane
	include(CORE.'/class/Controller.php');
	include(ROOT.'/controllers/AppController.php');

	/**
	 * Inicia o sistema de roteamento.
	 */
	$request = new RequestHandler($route);


	/**
	 * Inicia o controlador.
	 */
	if($request->valid){
		$controller_name = $request->controller_name."Controller";
		include(ROOT.'/controllers/'.$controller_name.'.php');
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
