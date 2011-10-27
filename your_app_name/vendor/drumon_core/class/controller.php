<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Main controller class
 *
 * @package class
 */
class Controller {

	/**
	 * Main Application object
	 *
	 * @var App
	 */
	private $app;

	/**
	 * View object
	 *
	 * @var View
	 */
	private $view;

	/**
	 * Request object
	 *
	 * @var Request
	 */
	public $request;

	/**
	 * Response object
	 *
	 * @var Response
	 */
	public $response;

	/**
	 * Current application layout (default: default)
	 *
	 * @var string
	 */
	protected $layout = "default";

	/**
	 * List of params from HTTP request (GET, POST, PUT, DELETE)
	 *
	 * @var array
	 */
	protected $params = array();

	/**
	 * Content for layout
	 *
	 * @var string
	 */
	private $content_for_layout = null;

	/**
	 * List of helpers
	 *
	 * @var array
	 */
	public $helpers = array();

	/**
	 * List of actions to fire before one action
	 *
	 * @var array
	 */
	public $before_action = array();

	/**
	 * List of actions to fire after one action
	 *
	 * @var string
	 */
	public $after_action = array();

	/**
	 * CSRF protection status (default: true)
	 *
	 * @var bool
	 */
	public $csrf_protection = true;


	/**
	 * Setup a new application controller
	 *
	 * @param string $app App object
	 * @param string $request Request object
	 * @param string $response Response object
	 * @param string $view View object
	 * @return void
	 */
	public function __construct(&$app, &$request, &$response, &$view) {
		$this->app = $app;
		$this->request = $request;
		$this->response = $response;
		$this->params = $request->params;
		$this->view = $view;
	}

	/**
	 * Proccess controller
	 *
	 * @return object Response
	 */
	public function process() {
		$this->response->charset = $this->app->config['charset'];

		// Protect from CSRF
		if($this->csrf_protection && $this->app->block_request($this->request)) {
			$this->request->params['_token'] = REQUEST_TOKEN;
			$this->render_error(401);
		}

		// Set default view to render
		$action_name = $this->request->action_name;
		$this->view->params = $this->params;
		$this->render(App::to_underscore(str_replace('_', '/', $this->request->controller_name)) . '/' . $this->request->action_name);

		// Get AppController variables
		$app_controller_vars = get_class_vars('AppController');

		// Merge AppController hooks with active controller
		$this->before_action = array_merge($app_controller_vars['before_action'], $this->before_action);
		$this->after_action = array_merge($app_controller_vars['after_action'], $this->after_action);

		// Execute before_actions
		$this->execute_methods($this->before_action);
		// Execute main action
		$this->$action_name();
		// Execute after_actions
		$this->execute_methods($this->after_action);

		// Add helpers in controller to App instace
		$this->app->add_helpers(array_merge($app_controller_vars['helpers'], $this->helpers));

		// Set response body
		$this->response->body = $this->view->process($this->layout, $this->content_for_layout, $this->app->helpers, $this->request);

		// Return a Response object
		return $this->response;
	}

	/**
	 * Fire all methods added on before and after hooks
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
	 * Add variables to view
	 *
	 * @param string $name view variable name
	 * @param mixed $value
	 * @return void
	 */
	public function add($name, $value) {
		$this->view->add($name, $value);
	}

	/**
	 * Set a view to render
	 *
	 * @param string $view
	 * @return void
	 */
	public function render($view_name, $http_status_code = 200) {
		$this->view->view_file_path = $view_name;
		$this->response->http_status_code = $http_status_code;
	}

	/**
	 * Render one text
	 *
	 * @param string $text
	 * @return void
	 */
	public function render_text($text, $http_status_code = 200) {
		$this->content_for_layout = $text;
		$this->response->http_status_code = $http_status_code;
	}

	/**
	 * Get object view
	 *
	 * @return View
	 */
	public function get_view() {
		return $this->view;
	}

	/**
	 * Redirect to one URL or other controller
	 *
	 * Examples:
	 *
	 * $this->redirect('http://github.com');
	 * $this->redirect(array('controller'=>'users','action'=>'index'));
	 *
	 * @param string|array $location
	 * @param int $code
	 * @param bool $exit
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
	 * Create custom methods on demand to named redirect
	 *
	 * @param string $name
	 * @param string $arguments
	 * @return void
	 */
	public function __call($name, $arguments) {
		$named_route = str_replace('redirect_to_', '', $name);
		if (substr($name, 0, 12) === 'redirect_to_') {
			$this->redirect(APP_DOMAIN . $this->request->url_for($named_route, $arguments));
		} else {
			trigger_error('Method ' . $name . ' not exist', E_USER_ERROR);
		}
	}

	/**
	 * Set a flash message
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

	/**
	 * Render an error
	 *
	 * @param string $code
	 * @param string $file_name filename with the view (optional)
	 * @return void
	 */
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