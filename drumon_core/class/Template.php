<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe para trabalhar com arquivos de template onde todas as views mais complexas podem utilizar para facilitar na mudança de layout.
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class Template {
	
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
	public $gzip = true;
	
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

	/**
	 * Renderiza a página.
	 *
	 * @param string $filename - Arquivo da página a ser renderizada.
	 * @return string - Código fonte do template renderizado.
	 */
	public function renderPage($filename) {
		if($this->gzip && ini_get('zlib.output_compression') != 1){
			ob_start('ob_gzhandler');
		}	
		return $this->fetch($filename);
	}

	/**
	 * Helper para renderizar uma view dentro do template.
	 *
	 * @param string $view - Nome da view a ser renderizada.
	 * @return void
	 */
	public function render($view) {
		$view = ($view[0] === '/') ? $view : $this->fetch(VIEW.'/'.$view.'.php');
		echo $view;
	}

	/**
	 * Helper para renderizar uma partial dentro do template.
	 *
	 * @param string $view - Nome do Arquivo da view a ser renderizada.
	 * @return void
	 */
	public function partial($view) {
		echo $this->fetch(VIEW.'/'.$this->partial_path.$view.'.php');
	}

	/**
	 * Analisa o modelo especificado e retorna seu conteúdo.
	 *
	 * @param   string $___filename
	 * @return  string
	 */
	public function fetch($filename) {
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
	public function removeAll()	{
		$this->variables = array();
	}
}
?>
