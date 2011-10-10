<?php

	class PagesController extends AppController {
		
		public function index() {
			// Generate application secret key
			$this->add('app_secret', md5(uniqid()));
		}
		
	}
?>