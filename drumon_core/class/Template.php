<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 *
 * Classe para trabalhar com arquivos de template onde todas as views mais complexas podem utilizar para facilitar na mudança de layout.
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class Template{
	
	/** 
	 * Variáveis incluídas para serem utilizadas na view.
	 *
	 * @access private
	 * @var array
	 */
	private $variables   = array();
	
	/** 
	 * Armazena o status de utilização da livraria gzip.
	 *
	 * @access public
	 * @var boolean
	 */
	public $gzip       	 = true;
	
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
	 * Renderiza uma view dentro do template.
	 *
	 * @param string $view - Nome da view a ser renderizada.
	 * @return void
	 */
	public function render($view) {
		$view = ($view[0] === '/') ? $view : $this->fetch(VIEW.'/'.$view.'.php');
		echo $view;
	}

	/**
	 * Renderiza uma partial dentro do template.
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
	public function fetch($___filename) {
		ob_start();
		extract($this->variables, EXTR_REFS | EXTR_OVERWRITE);
		include($___filename);
		$___content = ob_get_contents();
		ob_end_clean();
		return $___content;
	}

	/**
	 * Obtém uma variável da instância SKTemplate.
	 *
	 * @param   string $name - Nome da variável.
	 * @return  mixed - Valor da variável.
	 */
	public function get($name)	{
		return $this->variables[$name];
	}

	/**
	 * Adiciona valores a uma variável do template.
	 *
	 * @param string $name - Nome da variável.
	 * @param mixed $value - Valor a ser atribuido a variável.
	 * @return void
	 */
	public function add($name, $value) {
		$this->variables[$name] = $value;
	}

	/**
	 * Limpa valores de uma variável do template.
	 *
	 * @param string $name - Índice de $variables a ser limpo
	 * @return void
	 */
	public function clear($name) {
		unset($this->variables[$name]);
	}

	/**
	 * Limpa todos os valores das variáveis do template.
	 *
	 * @return void
	 */
	public function clearAll()	{
		$this->variables = array();
	}

	// /**
	//  * 
	//  *
	//  * @param   string  $name - Nome do índice.
	//  * @param   string  $filename - Arquivo correspondente ao índice.
	//  * @return  string	
	//  */
	// function load($name, $filename)	{
	// 	//if (!is_file($filename)) { $this->variables[$name] = "<strong>{$filename}</strong> not found."; return; }
	// 	$content = $this->fetch($filename);
	// 	$this->variables[$name] = $content;
	// 	return $content;
	// }

	// /**
	//  * Analisa o arquivo especificado $filename com um array $dados.
	//  * Ideal para analisar pedaços de código.
	//  *
	//  * @param   string  $filename - Arquivo da página a ser renderizada.
	//  * @param   array   $data
	//  * @return  string - Retorna o arquivo processado. 
	//  */
	// function parse($filename, $data) {
	// 	$tpl = new Template();
	// 	foreach ($data as $k => $v)
	// 		$tpl->add($k, $v);
	// 	return $tpl->fetch($filename);
	// }
}
?>