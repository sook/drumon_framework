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
class Controller {
	
	
	/** 
	 * Arquivo de template a ser usado pelo controlador.
	 *
	 * @var string
	 */
	private $template;
	
	/** 
	 * Arquivo de layout a ser usado pelo controlador, padrão default.
	 *
	 * @var string
	 */
	protected $layout = "default";
	
	/** 
	 * Arquivos helpers a serem usados pelo controlador.
	 *
	 * @var array
	 */
	protected $helpers = array();
	
	
	/** 
	 * Contém os parâmetros passados na requisição HTTP (GET e POST).
	 *
	 * @var array
	 */
	protected $params = array();
	
	
	/**
	 * Namespace do controlados
	 *
	 * @var string
	 */
	protected $namespaces;
	
	/**
	 * Nome da classe
	 *
	 * @var string
	 */
	protected $class_name;
	
	/**
	 * Nome da view a ser renderizada.
	 *
	 * @var string
	 */
	private $view;
	
	
	/**
	 * Conteúdo para o layout.
	 *
	 * @var string
	 */
	private $content_for_layout = null;

	/**
	 * Instancia um novo template com as configurações, parâmetros e idioma padrões.
	 *
	 * @access public
	 * @param object $request - Instância do Request Handler.
	 * @param array $locale - Referência da variável com os dados de internacionalização.
	 */
	public function __construct($request, $template, $namespaces, $class_name) {
		$this->request = $request;
		$this->params = $request->params;
		$this->template = $template;
		$this->namespaces = $namespaces;
		$this->class_name = $class_name;
	}
	
	/**
	 * Executa ação carregando helpers, ações de filtro e renderiza a view referente a ação.
	 *
	 * @access public
	 * @param string $action_name - Ação a ser executada.
	 * @return void
	 */
	public function execute($action_name) {
		$this->view = $action_name;
		
		$this->before_filter();
		$this->$action_name();
		$this->after_filter();
		
		return $this->execute_render();
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
	 * Adiciona variáveis a ser utilizadas no template.
	 *
	 * @access public
	 * @param String $name - Nome da variável que será utilizada no template.
	 * @param Mixed $value - Valor que será adicionado a variável no template.
	 * @return void
	 */
	public function add($name, $value) {
		$this->template->add($name, $value);
	}
	
	/**
	 * Define a view a ser renderizada.
	 *
	 * @param string $view 
	 * @return void
	 */
	public function render($view) {
		$this->view = $view;
	}
	
	/**
	 * Seta o texto para ser renderizado como conteúdo.
	 *
	 * @param string $text 
	 * @return void
	 */
	public function render_text($text) {
		$this->content_for_layout = $text;
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
	 * Renderiza as Views.
	 *
	 * @access public
	 * @param string $content - Um conteúdo para renderizar.
	 * @return void
	 */
	public function execute_render() {
		// Seta os parametros da requisição no template.
		$this->template->params = $this->params;
		// Carrega os helpers
		$this->load_helpers();
		
		// Se não tem conteúdo setado no controller.
		if($this->content_for_layout === null) {
			// Se não começar com / então chama a convenção do framework.
			if ($this->view[0] != '/') {
				$this->view = '/app/views/'.Drumon::to_underscore($this->namespaces).'/'.Drumon::to_underscore($this->class_name).'/'.$this->view;
			}
			$html = $this->template->render_file(ROOT.$this->view.".php");
		}
		
		// Para não redenrizar layout.
		// Setar no controller: var $layout = null;
		if(!empty($this->layout)) {
			$this->template->add('content_for_layout',$html);
			$html = $this->template->render_file(ROOT.'/app/views/layouts/'.$this->layout.'.php');
		}
		
		return $html;
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
		$this->helpers = array_merge($this->helpers, $default_helpers);
		// Adiciona os helpers na view.
		foreach ($this->helpers as $helper) {
			$helper = trim($helper);
			$local = in_array($helper, $core_helpers) ? CORE : ROOT.'/app';
			require_once $local."/helpers/".strtolower($helper)."_helper.php";
			$class = $helper.'Helper';
			$this->template->add(strtolower($helper), new $class($this->request));
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
}
?>