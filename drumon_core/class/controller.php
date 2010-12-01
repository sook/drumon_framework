<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe abstrata que fornece suporte a classe base do controlador.
 *
 * @package class
 * @abstract
 * @author Sook contato@sook.com.br
 */
abstract class Controller {
	
	/** 
	 * Referência da variável com os dados de internacionalização.
	 *
	 * @access private
	 * @var string
	 */
	private	$locale = null;
	
	/** 
	 * Array com as informações pertencentes a aplicação.
	 *
	 * @access private
	 * @var array
	 */
	private	$config;
	
	/** 
	 * Arquivo de template a ser usado pelo controlador.
	 *
	 * @access protected
	 * @var string
	 */
	protected $template;
	
	/** 
	 * Arquivo de layout a ser usado pelo controlador, padrão default.
	 *
	 * @access protected
	 * @var string
	 */
	protected $layout = "default";
	
	/** 
	 * Arquivos helpers a serem usados pelo controlador.
	 *
	 * @access protected
	 * @var array
	 */
	protected $helpers = array();
	
	/** 
	 * Indica se a página será renderizada.
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $render = true;
	
	/** 
	 * Contém os parâmetros passados na requisição HTTP (GET e POST).
	 *
	 * @access protected
	 * @var array
	 */
	protected $params = array();

	/**
	 * Instancia um novo template com as configurações, parâmetros e idioma padrões.
	 *
	 * @access public
	 * @param object $request - Instância do Request Handler.
	 * @param array $locale - Referência da variável com os dados de internacionalização.
	 */
	public function __construct($request, $locale) {
		$this->locale = $locale;
		$this->params = $request->params;
		$this->template = new Template();
		$this->request = $request;
	}
	
	/**
	 * Executa ação carregando helpers, ações de filtro e renderiza a view referente a ação.
	 *
	 * @access public
	 * @param string $action - Ação a ser executada.
	 * @return void
	 */
	public function execute($action) {
		$this->before_filter();
		$this->$action();
		$this->after_filter();
		
		$this->render($action);
	}

	/**
	 * Executada antes de qualquer ação no controlador.
	 *
	 * @access public
	 * @return void
	 */
	public function before_filter() {}

	/**
	 * Executada posteriormente a qualquer ação no controlador.
	 *
	 * @access public
	 * @return void
	 */
	public function after_filter() {}

	/**
	 * Adiciona valores a variáveis utilizadas no template.
	 *
	 * @access public
	 * @param String $key - Chave conteiner que se tornará uma variável no template.
	 * @param Mixed $value - Valores que sernao adicionados a chave no template.
	 * @return void
	 */
	public function add($key, $value) {
		$this->template->add($key, $value);
	}

	/**
	 * Renderiza as Views.
	 *
	 * @access public
	 * @param string $view - View a ser renderizada.
	 * @return void
	 */
	public function render($view, $content = null) {
		$this->load_helpers();
		$this->template->params = $this->params;
		
		if($content == null){
			$view = $view[0] == '/' ? $view : '/app/views/'.Drumon::to_underscore($this->request->controller_name).'/'.$view;
			$content = $this->template->render_page(ROOT.$view.".php");
		}
		
		// Para não redenrizar layout.
		// Setar no controller: var $layout = null;
		if(!empty($this->layout)){
			$this->add('content',$content);
			$content = $this->template->fetch(ROOT.'/app/views/layouts/'.$this->layout.'.php');
		}
		
		Event::fire('before_render',array('content' => &$content));
		echo $content;
		Event::fire('after_render');
		die(); // Para garantir e não chamar 2 render.
	}
	
	/**
	 * Renderiza o texto passado como parâmetro.
	 *
	 * @param string $text 
	 * @return void
	 */
	public function render_text($text) {
		$this->render(null,$text);
	}

	/**
	 * Redireciona para url especificada.
	 *
	 * @access public
	 * @param string $url - Url de destino.
	 * @param boolean $full - Verificador de url completa.
	 * @return void
	 */
	function redirect($url,$full = false) {
		$url = $full ? $url : APP_DOMAIN.$url;
		header('Location: '.$url);
	}

	/**
	 * Passa valor de status para o cabeçalho.
	 *
	 * @access public
	 * @param string $status - Status de cabeçalho.
	 * @return void
	 */
	function header($status) {
		header($status);
	}


	/**
	 * Seta os helpers a serem carregados.
	 *
	 * @access public
	 * @param array $helpers - Helpers a serem carregados.
	 * @return void
	 */
	public function helpers($helpers)	{
		$helpers = is_array($helpers) ? $helpers : array($helpers);
		$this->helpers = array_merge($this->helpers, $helpers);
	}

	/**
	 * Carrega os helpers e adiciona os helpers na view.
	 *
	 * @access private
	 * @return void
	 */
	private function load_helpers() {
		// Helpers existentes no core.
		$core_helpers = array('Date','Html','Image','Text','Paginate','Url');
		// Transforma a string de helpers em uma array.
		$default_helpers = (AUTOLOAD_HELPERS === '') ? array() : explode(',',AUTOLOAD_HELPERS);
		// Junta os helpers padrões com os helpers setados no controlador.
		//	print_r($this->helpers);
		$this->helpers = array_merge($this->helpers, $default_helpers);
		//print_r($this->helpers);
		// Adiciona os helpers na view.
		foreach ($this->helpers as $helper) {
			$helper = trim($helper);
			$local = in_array($helper, $core_helpers) ? CORE : ROOT.'/app';
			require_once $local."/helpers/".$helper."Helper.php";
			$class = $helper.'Helper';
			$this->add(strtolower($helper), new $class($this->locale,$this->request));
		}

		// Adiciona os helpers requeridos em outros helpers.
		foreach ($this->helpers as $helper) {
			$helper = trim($helper);
			$helper = $this->template->get(strtolower($helper));
			foreach ($helper->uses as $name) {
				$name = strtolower($name);
				$helper->$name = $this->template->get($name);
			}
		}
	}
}?>