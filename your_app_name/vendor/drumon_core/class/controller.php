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
	 * Objeto da classe view usado pelo controller.
	 *
	 * @var string
	 */
	private $view;
	
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
	private $view_name;
	
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
	
	/**
	 * Http Status Code to response.
	 *
	 * @var string
	 */
	public $http_status_code = null;
	
	/**
	 * Lista de helpers usados na view.
	 *
	 * @var array
	 */
	public $helpers = array();
	
	/**
	 * Lista de ações que serão executadas antes da ação principal
	 *
	 * @var array
	 */
	public $before_action = array();
	
	/**
	 * Lista de ações que serão executadas depois da ação principal
	 *
	 * @var string
	 */
	public $after_action = array();
	
	/**
	 * Instancia um novo view com as configurações, parâmetros e idioma padrões.
	 *
	 * @access public
	 * @param object $request - Instância do Request Handler.
	 * @param array $locale - Referência da variável com os dados de internacionalização.
	 */
	public function __construct($app, $request, $view) {
		$this->app = $app;
		$this->request = $request;
		$this->params = $request->params;
		$this->view = $view;
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
		$this->view_name = $this->request->action_name;
		
		$action_name = $this->view_name;
		
		// Junta os hooks do app_controller com os do controller atual.
		$app_controller_vars = get_class_vars('AppController');
		$this->before_action = array_merge($app_controller_vars['before_action'], $this->before_action);
		$this->after_action = array_merge($app_controller_vars['after_action'], $this->after_action);
		
		// Executa os before_actions
		$this->execute_methods($this->before_action);
		// Executa a action principal
		$this->$action_name();
		// Executa os after_actions
		$this->execute_methods($this->after_action);
	}
	
	
	/**
	 * Executa os métodos adicionados no before e after action.
	 *
	 * @param array $methods 
	 * @return void
	 */
	private function execute_methods($methods) {
		foreach ($methods as $key => $value) {
			if (is_array($value)) {
				if (isset($value['only'])) {
					if ($this->request->action_name === $value['only']) {
						call_user_func(array($this,$key));
					}
				} elseif(isset($value['except'])) {
					if ($this->request->action_name !== $value['except']) {
						call_user_func(array($this,$key));
					}
				}
			} else {
				call_user_func(array($this,$value));
			}
		}
	}

	/**
	 * Adiciona variáveis a ser utilizadas no view.
	 *
	 * @access public
	 * @param String $name - Nome da variável que será utilizada no view.
	 * @param Mixed $value - Valor que será adicionado a variável no view.
	 * @return void
	 */
	public function add($name, $value) {
		$this->view->add($name, $value);
	}
	
	/**
	 * Define a view a ser renderizada.
	 *
	 * @param string $view 
	 * @return void
	 */
	public function render($view_name, $http_status_code = 200) {
		$this->view_name = $view_name;
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
	 * @return void
	 */
	public function redirect($uri) {
		if ($uri[0] === '/') {
			$uri = APP_DOMAIN.$uri;
		}
		
		header('Location: '.$uri);
		exit;
	}
	
	
	/**
	 * Create custom methods on demand. (named routes for redirect)
	 *
	 * @param string $name 
	 * @param string $arguments 
	 * @return string
	 */
	public function __call($name, $arguments) {
		$named_route = str_replace('redirect_to_','',$name);
		if(substr($name,0,12) === 'redirect_to_') {
			$this->redirect(APP_DOMAIN.$this->request->url_for($named_route, $arguments));
		}else{
			trigger_error('Method '.$name.' not exist', E_USER_ERROR);
		}
	}
	
	/**
	 * Seta uma mensagem para ser acessada em outra página mesmo depois de um redirecionamento.
	 *
	 * @param string $key 
	 * @param string $value 
	 * @return void
	 */
	public function flash($key, $value) {
		// TODO: Rever sistema de seção depois.
		if (!isset($_SESSION)) {
			session_start();
		}
		$_SESSION['flash'][$key] = $value;
	}
	
	public function render_erro($code, $file_name = null) {
		$this->http_status_code = $code;
		if (empty($file_name)) {
			if (is_array($this->request->routes[$code])) {
				$this->view_folder = $this->request->routes[$code][0];
				$this->view_name = $this->request->routes[$code][1];
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
		// Se setado para não redenrizar então para.
		if ($this->view_name === false) { return; }
		
		$this->view->params = $this->params; // Seta os parametros da requisição no view.
		$this->load_helpers(); // Carrega os helpers
		
		// Renderiza view se não foi setado conteúdo manualmente.
		if($this->content_for_layout === null) {
			// Se não começar com / então chama a convenção do framework.
			if ($this->view_name[0] != '/') {
				$this->view_name = '/app/views/'.App::to_underscore(str_replace('_','/',$this->view_folder)).'/'.$this->view_name;
			}
			$this->content_for_layout = $this->view->render_file(ROOT.$this->view_name.".php");
		}

		// Renderiza layout se possuir.
		if($this->layout) {
			$this->view->add('content_for_layout', $this->content_for_layout);
			$html = $this->view->render_file(ROOT.'/app/views/layouts/'.$this->layout.'.php');
			$this->app->fire_event('after_render_layout', array('layout' => &$html));
		} else {
			$html = $this->content_for_layout;
		}
		
		return $html;
	}

	/**
	 * Carrega os helpers e adiciona os helpers na view.
	 *
	 * @access private
	 * @return void
	 */
	private function load_helpers() {
		// Adiciona helpers setados no controller na app.
		$app_controller_vars = get_class_vars('AppController');
		$controller_helpers = array_merge($app_controller_vars['helpers'], $this->helpers);
		$this->app->add_helpers($controller_helpers);
		
		// Adiciona os helpers na view.
		foreach ($this->app->helpers as $helper_name => $helper_path) {
			require_once $helper_path;
			$class = ucfirst($helper_name).'Helper';
			$this->view->add($helper_name, new $class($this->request, $this->app->config['language']));
		}
		
		// Adiciona os helpers requeridos em outros helpers.
		foreach ($this->app->helpers as $helper_name => $helper_path) {
			$helper_name = trim($helper_name);
			$helper = $this->view->get(strtolower($helper_name));
			foreach ($helper->uses as $name) {
				$name = strtolower($name);
				$helper->$name = $this->view->get($name);
			}
		}
	}
}
?>