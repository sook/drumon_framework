<?php

	class PagesController extends AppController {
		
		public function index() {
			// Gera chave para o app_secret
			$this->add('app_secret', md5(uniqid()));
//			$this->redirect('http://google.com');
			//$this->render_erro(404);
			//$this->response->headers['Content-Type'] = 'application/pdf';
			//echo 'aadsds dsdsds';
		}
		
	}
	
?>