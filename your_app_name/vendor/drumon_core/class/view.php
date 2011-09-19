<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe para trabalhar com as views do Drumon Framework
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class View {
	
	/** 
	 * Variáveis incluídas para serem utilizadas na view.
	 *
	 * @access private
	 * @var array
	 */
	private $variables = array();
	
	/** 
	 * Se tiver true comprime o conteúdo do html usando Gzip.
	 *
	 * @access public
	 * @var boolean
	 */
	public $gzip;
	
	
	/** 
	 * Contém os parâmetros passados na requisição HTTP (GET e POST).
	 *
	 * @access public
	 * @var array
	 */
	public $params = array();
	
	
	public $view_file_path;
	
	
	public function __construct($variables = array(), $gzip = true)
	{
		$this->variables = $variables;
		$this->gzip = $gzip;
	}
	
	public function process($layout, $content_for_layout, $helpers = array(), &$request = null)
	{
		if ($this->gzip && ini_get('zlib.output_compression') != 1) {
			ob_start('ob_gzhandler'); // TODO: se 304 Not Modified não pode usar isso.
		}
		
		if ($this->view_file_path === false) { return; }
		
		$this->load_helpers($helpers, $request);
		
		// Renderiza view se não foi setado conteúdo manualmente.
		if ($content_for_layout === null) {
			// Se não começar com / então chama a convenção do framework.
			if ($this->view_file_path[0] != '/') {
				$this->view_file_path = '/app/views/'.$this->view_file_path;
			}
			$content_for_layout = $this->render_file(APP_PATH . $this->view_file_path);
		}

		// Renderiza o layout se possuir.
		if ($layout) {
			$this->add('content_for_layout', $content_for_layout.PHP_EOL);
			$html = $this->render_file(APP_PATH.'/app/views/layouts/'.$layout.'.php');
			
			$app = App::get_instance();
			$app->fire_event('after_render_layout', array('layout' => &$html));
			
			return $html;
		} else {
			return $content_for_layout;
		}
	}

	/**
	 * Helper para renderizar uma view dentro do template.
	 *
	 * @param string $view - Nome da view a ser renderizada.
	 * @return void
	 */
	public function render($view)
	{
		return ($view[0] === '/') ? $this->render_file(APP_PATH.$view.'.php') : $this->render_file(APP_PATH.'/app/views/'.$view.'.php');
	}

	/**
	 * Obtém o conteúdo do arquivo processado.
	 *
	 * @param   string $filename
	 * @return  string
	 */
	public function render_file($filename)
	{
		ob_start();
		extract($this->variables, EXTR_REFS | EXTR_OVERWRITE);
		include($filename);
		$result = ob_get_contents();
		ob_end_clean();
		return $result;
	}

	/**
	 * Obtém uma variável adicionada na view.
	 *
	 * @param   string $name - Nome da variável.
	 * @return  mixed - Valor da variável.
	 */
	public function get($name) {
		return $this->variables[$name];
	}
	
	/**
	 * Obtém todas as variáveis.
	 *
	 * @param   string $name - Nome da variável.
	 * @return  mixed - Valor da variável.
	 */
	public function get_all()	{
		return $this->variables;
	}

	/**
	 * Adiciona uma variável a view.
	 *
	 * @param string $name - Nome da variável.
	 * @param mixed $value - Valor a ser atribuído a variável.
	 * @return void
	 */
	public function add($name, $value) {
		$this->variables[$name] = $value;
	}
	
	/**
	 * Remove uma variável do template.
	 *
	 * @param string $name - Índice de $variables a ser limpo
	 * @return void
	 */
	public function remove($name) {
		unset($this->variables[$name]);
	}

	/**
	 * Remove todas as variáveis do template.
	 *
	 * @return void
	 */
	public function remove_all()	{
		$this->variables = array();
	}
	
	// TODO: rever sistema de seção e flash depois.
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
	
	
	public function check_flash($key) {
	  if (!isset($_SESSION)) {
	    session_start();
	  }
	  return isset($_SESSION['flash'][$key]);
	}
	
	/**
	 * Carrega os helpers e adiciona os helpers na view.
	 *
	 * @access private
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