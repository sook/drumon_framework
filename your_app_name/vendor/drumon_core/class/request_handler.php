<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Class to add route system in ours applications.
 * 
 * This class is based on DooPHP Router and dan (http://blog.sosedoff.com/) url router.
 * @package class
 * @author Sook contato@sook.com.br
 */
class RequestHandler {
	/** 
	 * The controller name.
	 *
	 * @var string
	 */
	public $controller_name;
	
	/** 
	 * The action name to execute from your application.
	 *
	 * @var string
	 */
	public $action_name;
	
	/** 
	 * Array with all params in http request (GET e POST).
	 *
	 * @var array
	 */
	public $params = array();
	
	/** 
	 * Where from last request.
	 *
	 * @var string
	 */
	public $referer;
	
	/**
	 * Same the $_SERVER['request_uri']
	 *
	 * @var string
	 */
	public $uri;
	
	/** 
	 * The request method. (GET,POST,PUT,DELETE).
	 *
	 * @var string
	 */
	public $method;
	
	/**
	 * List of routes
	 *
	 * @var string
	 */
	public $routes;

	/**
	 * Start request handler
	 *
	 * @param array $routes Route list
	 * @param string $app_path Application folder path.
	 * @access public
	 */
	public function __construct($routes, $app_path) {
		$this->routes = $routes;
		$this->app_path = $app_path;
		$this->method = (isset($_REQUEST['_method']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') ? strtolower($_REQUEST['_method']) : strtolower($_SERVER['REQUEST_METHOD']);
	}
	
	/**
	 * Check if route is valid
	 *
	 * @return boolean
	 */
	public function valid() {
		if($route = $this->find_request_route()) {
			if(isset($route['redirect'])) {
				if(!isset($route[0])) { $route[0] = null; }
				$this->redirect($route['redirect'], $route[0]);
			} else {
				list($controller, $action) = explode('::', $route[0]);
				$this->controller_name = $controller;
				$this->action_name = $action;
				$this->params = array_merge($this->params, $_GET, $_POST);
				if(isset($_SERVER['HTTP_REFERER'])) { $this->referer = $_SERVER['HTTP_REFERER']; }
				$this->uri = $_SERVER['REQUEST_URI'];
			}
			return true;
		}
		return false;
	}

	/**
	 * Search for a valid route.
	 *
	 * @access public
	 * @return mixed - Route value if found or false if not found.
	 */
	public function find_request_route() {
		$app_folder = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\','/', $this->app_path));
		$request_route = explode('?', str_replace($app_folder, '', $_SERVER['REQUEST_URI']));
		$request_route = $this->remove_last_slash($request_route[0]);

		// Fast root match
		if($request_route === '' || $request_route === '/index.php') {
			if(isset($this->routes[$this->method]['/'])) return $this->routes[$this->method]['/'];
			if(isset($this->routes['*']['/'])) return $this->routes['*']['/'];
			return false;
		}
		
		// Merge routes
		$route_list = array();
		// Get generic routes
		if(isset($this->routes['*'])) { $route_list = $this->routes['*']; }
		// Pega as rotas defindas com o método requisitado e junta com o geral.
		if(isset($this->routes[$this->method])) { $route_list = array_merge($route_list, $this->routes[$this->method]); }
		// Se não existe rota já retorna false.
		if(empty($route_list)) { return false; }
		
		// Return match static route
		if(isset($route_list[$request_route])) {
			return $route_list[$request_route];
		} elseif(isset($route_list[$request_route.'/'])) {
			return $route_list[$request_route.'/'];
		}

		$request_route = substr($request_route, 1);
		$request_parts = explode('/', $request_route);	
		
		foreach ($route_list as $route => $route_values) {
			$route = $this->remove_last_slash(substr($route,1));
			$route_parts = explode('/', $this->remove_last_slash($route));

			// Next if is root
			if(!$route) continue;
			// Next if route parts not equal
			if(sizeof($request_parts) !== sizeof($route_parts) ) continue;
			
			// 			// Pula se parte estática for diferente.
			// 			$static = explode(':',$route);
			// 			if(substr($uri[0],0,strlen($static[0]))!==$static[0]) continue;
			
			// Pega as variaveis da rota
			preg_match_all('/{([\w]+)}/', $route, $params_name, PREG_PATTERN_ORDER);
			// Se não tem variável então pula.
			if(!count($params_name[0])) continue;
			
			$route_regex = $route;
			$route_regex = str_replace('/','\/',$route_regex);
			foreach ($params_name[0] as $name) {
				$regex = '[.a-zA-Z0-9_\+\-%]';
				if (isset($route_values[$name])) $regex = $route_values[$name];
				$route_regex = str_replace($name, '('.$regex.'*)', $route_regex);
			}
			$route_regex .= '$';
			
			if(preg_match('/'.$route_regex.'/' , $request_route, $matches) === 1) {
				// Remove o primeiro valor encontrado no preg_march.
				array_shift($matches);
				
				// Se o numero de paramentros da rota for diferente da encontrada pula.
				if (count($params_name[1]) != count($matches)) continue;
				foreach ($params_name[1] as $key => $value) {
					$this->params[$value] = $matches[$key];
				}
				return $route_values;
			}
		}
		return false;
	}
	
	
	public function __call($name, $arguments) {
		$named_route = str_replace('_path','',$name);
		if(substr($name,-5,strlen($name)-1) === '_path') {
			return $this->url_for($named_route,$arguments);
		}
		trigger_error('Method '.$name.' not exist', E_USER_ERROR);
	}
	
	/**
	 * Return url found in route
	 *
	 * @param string $named_route 
	 * @param string $params 
	 * @return string
	 */
	public function url_for($named_route, $params = array()) {
		if (isset($this->routes['*']) && $path = $this->find_route($this->routes['*'], $named_route, $params)) {
		} elseif(isset($this->routes['get']) && $path = $this->find_route($this->routes['get'],$named_route,$params)) {
		} elseif(isset($this->routes['put']) && $path = $this->find_route($this->routes['put'],$named_route,$params)) {
		} elseif(isset($this->routes['post']) && $path = $this->find_route($this->routes['post'],$named_route,$params)){}
		if(!$path) trigger_error('Named route for '.$named_route.' doenst exist', E_USER_ERROR);
		return str_replace('\/','/',$path); ;
	}
	
	/**
	 * Search route with your name
	 *
	 * @param string $route_list 
	 * @param string $named_route 
	 * @param string $params 
	 * @return string
	 */
	public function find_route($route_list, $named_route, $params = array()) {
		$path = false;
		foreach ($route_list as $url => $route) {
			if(isset($route['as']) && $route['as'] == $named_route ) {
				
				// Pega as variaveis dinamicas
				preg_match_all('/{([\w]+)}/', $url, $params_name, PREG_PATTERN_ORDER);
				$params_name = $params_name[0];
				
				// Se não tem variável então pula.
				if(count($params_name)){
					if (count($params) != count($params_name)) { die('Named route for '.$named_route.' expects '.count($params_name).' params not '.count($params).'.'); }
					$route_regex = $url;
					$route_regex = str_replace('/','\/', $route_regex);
					
					$i = 0;
					foreach ($params_name as $name) {
						$route_regex = preg_replace('/'.$name.'/', $params[$i], $route_regex,1);
						$i++;
					}
					$path = $route_regex;
				}else{
					$path = $url;
				}
			}
		}
		return $path;
	}
	
	/**
	 * Redirect to external url, with http header 302 
	 *
	 * @access public
	 * @param string $location - URL of the redirect location.
	 * @param code $code - HTTP status code to be sent with the header.
	 * @param boolean $exit - To end the application.
	 * @param array $headerBefore - Headers to be sent before header("Location: some_url_address").
	 * @param array $headerAfter - Headers to be sent after header("Location: some_url_address").
	 * @return void
	 */
	public function redirect($location, $code = 302, $exit = true, $headerBefore = NULL, $headerAfter = NULL) {
		if($headerBefore != NULL) {
			for($i=0; $i < sizeof($headerBefore); $i++) {
				header($headerBefore[$i]);
			}
		}
		header("Location: $location", true, $code);
		if($headerAfter != NULL) {
			for($i=0; $i < sizeof($headerBefore); $i++) {
				header($headerBefore[$i]);
			}
		}
		if($exit) die;
	}

	/**
	 * Remove the slash(/) from the last char.
	 *
	 * @access public
	 * @param string $str - String com (/) a ser alterada.
	 * @return string - String sem a barra (/).
	 */
	protected function remove_last_slash($str) {
		if($str[strlen($str)-1]==='/') {
			$str = substr($str,0,-1);
		}
		return $str;
	}
}
?>
