<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * View class
 *
 * @package class
 */
class View {
	
	/** 
	 * Variables used in views
	 *
	 * @var array
	 */
	private $variables = array();
	
	/** 
	 * Gzip result if true
	 *
	 * @var bool
	 */
	public $gzip;
	
	
	/** 
	 * List of params from HTTP request (GET, POST, PUT, DELETE)
	 *
	 * @var array
	 */
	public $params = array();
	
	/**
	 * File to render a view
	 *
	 * @var string
	 */
	public $view_file_path;
	
	/**
	 * Setup new view
	 *
	 * @param string $variables 
	 * @param string $gzip 
	 */
	public function __construct($variables = array(), $gzip = true) {
		$this->variables = $variables;
		$this->gzip = $gzip;
	}
	
	/**
	 * Proccess render
	 *
	 * @param string $layout 
	 * @param string $content_for_layout 
	 * @param string $helpers 
	 * @param object $request 
	 * @return string rendered view
	 */
	public function process($layout, $content_for_layout, $helpers = array(), &$request = null) {
		if ($this->gzip && ini_get('zlib.output_compression') != 1) {
			ob_start('ob_gzhandler'); // TODO: if 304 Not Modified dont use this
		}
		
		if ($this->view_file_path === false) { return; }
		
		$this->load_helpers($helpers, $request);
		
		// Render view if dont have content
		if ($content_for_layout === null) {
			// Se não começar com / então chama a convenção do framework.
			if ($this->view_file_path[0] != '/') {
				$this->view_file_path = '/app/views/' . $this->view_file_path;
			}
			$content_for_layout = $this->render_file(APP_PATH . $this->view_file_path);
		}

		// Render layout
		if ($layout) {
			$this->add('content_for_layout', $content_for_layout . PHP_EOL);
			$html = $this->render_file(APP_PATH . '/app/views/layouts/' . $layout . '.php');
			
			$app = App::get_instance();
			$app->fire_event('after_render_layout', array('layout' => &$html));
			
			return $html;
		} else {
			return $content_for_layout;
		}
	}

	/**
	 * Render other view inside view
	 *
	 * @param string $view 
	 * @return string
	 */
	public function render($view) {
		return ($view[0] === '/') ? $this->render_file(APP_PATH . $view . '.php') : $this->render_file(APP_PATH . '/app/views/' . $view . '.php');
	}

	/**
	 * Render one file and return your content
	 *
	 * @param   string $filename
	 * @return  string
	 */
	public function render_file($filename) {
		ob_start();
		extract($this->variables, EXTR_REFS | EXTR_OVERWRITE);
		include($filename);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	/**
	 * Get view variable 
	 *
	 * @param   string $name
	 * @return  mixed
	 */
	public function get($name) {
		return $this->variables[$name];
	}
	
	/**
	 * Get all view variables
	 *
	 * @param string $name
	 * @return  mixed
	 */
	public function get_all() {
		return $this->variables;
	}

	/**
	 * Add one variable to view
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function add($name, $value) {
		$this->variables[$name] = $value;
	}
	
	/**
	 * Remove one variable from view
	 *
	 * @param string $name
	 * @return void
	 */
	public function remove($name) {
		unset($this->variables[$name]);
	}

	/**
	 * Remove all view variables
	 *
	 * @return void
	 */
	public function remove_all() {
		$this->variables = array();
	}
	
	// TODO: rever sistema de seção e flash depois.
	/**
	 * Get flash session
	 *
	 * @param string $key Key name
	 * @return mixed
	 */
	public function flash($key) {
		
		if (!isset($_SESSION)) {
			session_start();
		}
		
		if (!isset($_SESSION['flash'])) {
			$_SESSION['flash'] = array();
		}
		
		if (isset($_SESSION['flash'][$key])) {
			$value = $_SESSION['flash'][$key];
			unset($_SESSION['flash'][$key]);
			return $value;
		}else{
			return false;
		}
	}
	
	/**
	 * Check if flash session exist
	 *
	 * @param string $key 
	 * @return bool
	 */
	public function check_flash($key) {
	  if (!isset($_SESSION)) {
	    session_start();
	  }
	  return isset($_SESSION['flash'][$key]);
	}
	
	/**
	 * Load helpers and set on view
	 *
	 * @return void
	 */
	private function load_helpers($helpers, &$request) {
		
		// Adiciona os helpers na view.
		foreach ($helpers as $helper_name => $helper_path) {
			require_once $helper_path;
			$class = ucfirst($helper_name).'Helper';
			$this->add($helper_name, new $class($request));
		}
		
		// Adiciona os helpers requeridos em outros helpers.
		// TODO: Se for o helper dentro for um que não foi carregado dará um erro.
		foreach ($helpers as $helper_name => $helper_path) {
			$helper_name = trim($helper_name);
			$helper = $this->get(strtolower($helper_name));
			foreach ($helper->uses as $name) {
				$name = strtolower($name);
				$helper->$name = $this->get($name);
			}
		}
	}
}
?>