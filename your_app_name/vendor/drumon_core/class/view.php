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
	 * Armazena o diretório padrão das partials.
	 *
	 * @access public
	 * @var string
	 */
	public $partial_path = 'partials/';
	
	/** 
	 * Contém os parâmetros passados na requisição HTTP (GET e POST).
	 *
	 * @access public
	 * @var array
	 */
	public $params = array();
	
	
	public function __construct($variables = array(), $gzip = true) {
		$this->variables = $variables;
		$this->gzip = $gzip;
		
		if($this->gzip && ini_get('zlib.output_compression') != 1) {
			ob_start('ob_gzhandler'); // TODO: se 304 Not Modified não pode usar isso.
		}
	}

	/**
	 * Helper para renderizar uma view dentro do template.
	 *
	 * @param string $view - Nome da view a ser renderizada.
	 * @return void
	 */
	public function render($view) {
		return ($view[0] === '/') ? $this->render_file(ROOT.$view.'.php') : $this->render_file(ROOT.'/app/views/'.$view.'.php');
	}

	/**
	 * Helper para renderizar uma partial dentro do template.
	 *
	 * @param string $view - Nome do Arquivo da view a ser renderizada.
	 * @return void
	 */
	public function partial($view) {
		return $this->render_file(ROOT.'/app/views/'.$this->partial_path.$view.'.php');
	}

	/**
	 * Obtem o conteúdo do arquivo processado.
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
	 * Obtém uma variável adicionada na view.
	 *
	 * @param   string $name - Nome da variável.
	 * @return  mixed - Valor da variável.
	 */
	public function get($name)	{
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
}
?>