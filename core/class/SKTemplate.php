<?php
/**
 * Classe para trabalhar com arquivos de template onde todas as views mais complexas podem utilizar para facilitar na mudança de layout.
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class SKTemplate{
	
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
	 * Renderiza a página
	 *
	 * @param string $filename Arquivo da página a ser renderizada.
	 * @return array
	 */
	public function renderPage($filename) {
		if($this->gzip && ini_get('zlib.output_compression') != 1)	ob_start('ob_gzhandler');
		return $this->fetch($filename);
	}

	/**
	 * Renderiza view usando uma página específica.
	 *
	 * @param string $view Nome da view a ser renderizada
	 * @return void
	 */
	public function render($view) {
		$view = ($view[0] === '/') ? $view : $this->fetch(VIEW.'/'.$view.'.php');
		echo $view;
	}

	/**
	 * Renderiza qualquer parte especificada da página especificada.
	 *
	 * @param string $view Nome do Arquivo da view a ser renderizada
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
	 * Obtém a variável especificada atribuída.
	 *
	 * @param   string $name Nome da variável
	 * @return  mixed
	 */
	public function get($name)	{
		return $this->variables[$name];
	}

	/**
	 * Adiciona valores ao índice de $variables[$name].
	 *
	 * @param string $name Nome da variável
	 * @param mixed $value Valor a ser atribuido a variável 
	 * @return void
	 */
	public function add($name, $value) {
		$this->variables[$name] = $value;
	}

	/**
	 * Limpa os valores do índice de $variables[$name].
	 *
	 * @param string $name Índice de $variables a ser limpo
	 * @return void
	 */
	public function clear($name) {
		unset($this->variables[$name]);
	}

	/**
	 * Limpa todas os índices de $variables[].
	 *
	 * @return void
	 */
	public function clearAll()	{
		$this->variables = array();
	}

	/**
	 * Carrega o conteúdo dos índices de $variables.
	 *
	 * @param   string  $name - Nome do índice.
	 * @param   string  $filename - Arquivo correspondente ao índice.
	 * @return  string
	 */
	function load($name, $filename)	{
		//if (!is_file($filename)) { $this->variables[$name] = "<strong>{$filename}</strong> not found."; return; }
		$content = $this->fetch($filename);
		$this->variables[$name] = $content;
		return $content;
	}

	/**
	 * Analisa o arquivo especificado $filename com um array $dados.
	 * Ideal para analisar pedaços de código.
	 *
	 * @param   string  $filename - Arquivo da página a ser renderizada.
	 * @param   array   $data
	 * @return  string
	 */
	function parse($filename, $data) {
		$tpl = new Template();
		foreach ($data as $k => $v)
		$tpl->add($k, $v);
		return $tpl->fetch($filename);
	}
}
?>
