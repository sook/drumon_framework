<?php

	class HomeController extends AppController {
	
		function index() {
			// Gera chave para o app_secret
			$this->add('app_secret', md5(uniqid()));
		}
	}
	
?>