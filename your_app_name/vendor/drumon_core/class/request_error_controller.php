<?php
	
	/**
	 * Default request error controller
	 *
	 * @package class
	 */
	class RequestErrorController extends AppController {
		
		public $layout = null;
		
		public function error_404() {
			$this->render('error/404', 404);
		}
		
		public function error_401() {
			$this->render('error/401', 401);
		}
		
	}
	
?>