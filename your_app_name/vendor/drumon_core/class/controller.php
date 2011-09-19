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
	
	private $app;
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
	 * Conteúdo para o layout.
	 *
	 * @var string
	 */
	private $content_for_layout = null;
	
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
	 * Proteção contra CSRF ligada.
	 *
	 * @var boolean
	 */
	public $csrf_protection = true;
	
	public $response;
	public $request;
	
	/**
	 * Instancia um novo view com as configurações, parâmetros e idioma padrões.
	 *
	 * @access public
	 * @param object $request - Instância do Request Handler.
	 * @param array $locale - Referência da variável com os dados de internacionalização.
	 */
	public function __construct(&$app, &$request, &$response, &$view) {
		$this->app = $app;
		$this->request = $request;
		$this->response = $response;
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
	public function process()
	{
		$this->response->charset = $this->app->config['charset'];
		
		// Se for uma requisição perigosa renderinza uma página de erro
		if($this->csrf_protection && $this->app->block_csrf_protection($this->request)) {
			$this->request->params['_token'] = REQUEST_TOKEN;
			$this->render_error(401);
		}
		
		// Set default view to render
		$action_name = $this->request->action_name;
		$this->view->params = $this->params;
		$this->render(App::to_underscore(str_replace('_', '/', $this->request->controller_name)) . '/' . $this->request->action_name);
		
		// Get AppController variables.
		$app_controller_vars = get_class_vars('AppController');
		
		// Junta os hooks do app_controller com os do controller atual.
		$this->before_action = array_merge($app_controller_vars['before_action'], $this->before_action);
		$this->after_action = array_merge($app_controller_vars['after_action'], $this->after_action);
		
		// Executa os before_actions
		$this->execute_methods($this->before_action);
		// Executa a action principal
		$this->$action_name();
		// Executa os after_actions
		$this->execute_methods($this->after_action);
		
		// Adiciona helpers setados no controller na app.
		$this->app->add_helpers(array_merge($app_controller_vars['helpers'], $this->helpers));
		
		// Set response body
		$this->response->body = $this->view->process($this->layout, $this->content_for_layout, $this->app->helpers, $this->request);
		
		// Return a Response object
		return $this->response;
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
					if (is_array($value['only'])) {
						foreach ($value['only'] as $only) {
							if ($this->request->action_name === $only) {
								call_user_func(array($this, $key));
							}
						}
					} else {
						if ($this->request->action_name === $value['only']) {
							call_user_func(array($this, $key));
						}
					}
					
				} elseif(isset($value['except'])) {
					if (is_array($value['except'])) {
						foreach ($value['except'] as $except) {
							if ($this->request->action_name !== $except) {
								call_user_func(array($this, $key));
							}
						}
					} else {
						if ($this->request->action_name !== $value['except']) {
							call_user_func(array($this, $key));
						}
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
		$this->view->view_file_path = $view_name.'.php';
		$this->response->http_status_code = $http_status_code;
	}
	
	/**
	 * Seta o texto para ser renderizado como conteúdo.
	 *
	 * @param string $text 
	 * @return void
	 */
	public function render_text($text, $http_status_code = 200) {
		$this->content_for_layout = $text;
		$this->response->http_status_code = $http_status_code;
	}
	
	public function get_view() {
		return $this->view;
	}

	/**
	 * Redireciona para url especificada.
	 *
	 * @access public
	 * @param string $url - Url de destino.
	 * @return void
	 */
	public function redirect($location, $code = 302, $exit = true) {
		
		if (is_array($location)) {
			$this->request->controller_name = $location['controller'];
			$this->request->action_name = $location['action'];
			$this->app->proccess_controller($this->request);
			exit;
		}
		
		if ($location[0] === '/') {
			$location = APP_DOMAIN . $location;
		}
		
		$this->request->redirect($location, $code, $exit);
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
	
	public function render_error($code, $file_name = null) {
		
		$this->response->http_status_code = $code;
		
		if (!empty($file_name)) {
			$this->layout = null;
			$this->view->view_file_path = $file_name;
			return;
		}
		
		list($controller_name, $action_name) = explode('::', $this->request->routes[$code][0]);
		$this->redirect(array('controller' => $controller_name, 'action' => $action_name));
		
	}
	
}
?>