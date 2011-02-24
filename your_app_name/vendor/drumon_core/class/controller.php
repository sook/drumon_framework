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
	 * Nome da pasta onde a view do controller está.
	 *
	 * @var string
	 */
	private $view_folder;
	
	
	/**
	 * Conteúdo para o layout.
	 *
	 * @var string
	 */
	private $content_for_layout = null;
	
	
	public $http_status_code = null;
	
	/**
	 * Instancia um novo template com as configurações, parâmetros e idioma padrões.
	 *
	 * @access public
	 * @param object $request - Instância do Request Handler.
	 * @param array $locale - Referência da variável com os dados de internacionalização.
	 */
	public function __construct($app, $request, $template) {
		$this->app = $app;
		$this->request = $request;
		$this->params = $request->params;
		$this->template = $template;
	//	$this->namespaces = $namespaces;
	//	$this->class_name = $class_name;
	}
	
	/**
	 * Executa ação carregando helpers, ações de filtro e renderiza a view referente a ação.
	 *
	 * @access public
	 * @param string $action_name - Ação a ser executada.
	 * @return void
	 */
	public function execute_action() {
		$this->view_folder = $this->request->controller_name;
		$this->view = $this->request->action_name;
		
		$action_name = $this->view;
		
		$this->before_filter();
		$this->$action_name();
		$this->after_filter();
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
	public function render($view, $http_status_code = 200) {
		$this->view = $view;
		$this->http_status_code = $http_status_code;
	}
	
	
	public function http_status($http_status_code) {
		$this->http_status_code = $http_status_code;
	}
	
	/**
	 * Seta o texto para ser renderizado como conteúdo.
	 *
	 * @param string $text 
	 * @return void
	 */
	public function render_text($text, $http_status_code = 200) {
		$this->content_for_layout = $text;
		$this->http_status_code = $http_status_code;
	}

	/**
	 * Redireciona para url especificada.
	 *
	 * @access public
	 * @param string $url - Url de destino.
	 * @param boolean $full - Verificador de url completa.
	 * @return void
	 */
	public function redirect($url,$full = false) {
		$url = $full ? $url : APP_DOMAIN.$url;
		header('Location: '.$url);
	}
	
	
	public function render_erro($code, $file_name = null) {
		$this->http_status_code = $code;
		if (empty($file_name)) {
			if (is_array($this->request->routes[$code])) {
				$this->view_folder = $this->request->routes[$code][0];
				$this->view = $this->request->routes[$code][1];
			} else {
				$this->layout = null;
				$this->content_for_layout = file_get_contents(ROOT.'/public/'.$this->request->routes[$code]);
			}
		} else {
			$this->layout = null;
			$this->content_for_layout = file_get_contents(ROOT.'/public/'.$file_name);
		}
	}

	/**
	 * Renderiza as Views.
	 *
	 * @access public
	 * @param string $content - Um conteúdo para renderizar.
	 * @return void
	 */
	public function execute_view() {
		$this->template->params = $this->params; // Seta os parametros da requisição no template.
		$this->load_helpers(); // Carrega os helpers
		
		// Renderiza view se não foi setado conteúdo manualmente.
		if($this->content_for_layout === null) {
			// Se não começar com / então chama a convenção do framework.
			if ($this->view[0] != '/') {
				$this->view = '/app/views/'.App::to_underscore(str_replace('_','/',$this->view_folder)).'/'.$this->view;
			}
			$this->content_for_layout = $this->template->render_file(ROOT.$this->view.".php");
		}

		// Renderiza layout se possuir.
		if(!empty($this->layout)) {
			$this->template->add('content_for_layout', $this->content_for_layout);
			$html = $this->template->render_file(ROOT.'/app/views/layouts/'.$this->layout.'.php');
		} else {
			$html = $this->content_for_layout;
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
	public function add_helpers($helpers)	{
		$this->app->add_helpers($helpers);
	}

	/**
	 * Carrega os helpers e adiciona os helpers na view.
	 *
	 * @access private
	 * @return void
	 */
	private function load_helpers() {
		// Helpers existentes no core.
		$core_helpers = array('date','html','image','text','paginate','url');

		// Adiciona os helpers na view.
		foreach ($this->app->helpers as $helper) {
			$helper = strtolower(trim($helper));
			$local = in_array($helper, $core_helpers) ? CORE : ROOT.'/app';
			require_once $local."/helpers/".$helper."_helper.php";
			$class = ucfirst($helper).'Helper';
			$this->template->add($helper, new $class($this->request, $this->app->config['language']));
		}

		// Adiciona os helpers requeridos em outros helpers.
		foreach ($this->app->helpers as $helper) {
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