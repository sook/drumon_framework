<?php

	class PagesController extends AppController {
		
		public function index() {
			// Gera chave para o app_secret
			$this->add('app_secret', md5(uniqid()));
		}
		
	}
	
?>