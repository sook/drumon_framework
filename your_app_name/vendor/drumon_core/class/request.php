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
class Request {
	
	/** 
	 * Requested controller name
	 *
	 * @var string
	 */
	public $controller_name;
	
	/** 
	 * Requested action name
	 *
	 * @var string
	 */
	public $action_name;
	
	/** 
	 * List with all params from HTTP Request (GET, POST)
	 *
	 * @var array
	 */
	public $params = array();
	
	/** 
	 * Request referer
	 *
	 * @var string
	 */
	public $referer;
	
	/**
	 * Requested URI
	 *
	 * @var string
	 */
	public $uri;
	
	/** 
	 * The request method. (GET, POST, PUT, DELETE).
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
	 * Request is valid
	 *
	 * @var bool
	 */
	public $valid = false;

	/**
	 * Start request handler
	 *
	 * @param array $routes Route list
	 * @param string $app_path Application folder path.
	 */
	public function __construct($routes, $app_path) {
		$this->routes = $routes;
		$this->app_path = $app_path;
		$this->method = (isset($_REQUEST['_method']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') ? strtolower($_REQUEST['_method']) : strtolower($_SERVER['REQUEST_METHOD']);
	}
	
	/**
	 * Setup request
	 *
	 * @return void
	 */
	public function validate() {
		if (!$route = $this->find_request_route()) {
			list($this->controller_name, $this->action_name) = explode('::', $this->routes['404'][0]);
			return $this->valid;
		}
		
		$this->valid = true;
				
		if (isset($route['redirect'])) {
			if (!isset($route[0])) $route[0] = null;
			$this->redirect($route['redirect'], $route[0]);
		} else {
			list($this->controller_name, $this->action_name) = explode('::', $route[0]);
			$this->params = array_merge($this->params, $_GET, $_POST);
			
			if (isset($_SERVER['HTTP_REFERER'])) $this->referer = $_SERVER['HTTP_REFERER'];
			$this->uri = $_SERVER['REQUEST_URI'];
		}
		
		return $this->valid;
	}

	/**
	 * Search for a valid route.
	 *
	 * @access public
	 * @return array - Route data
	 */
	public function find_request_route() {
		$root = dirname($_SERVER['SCRIPT_NAME']);
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if ($root !== '/') {
	            $path = str_replace($root, '', $path);
	        }
	
		$request_route = $path;

		// Fast root match
		if ($request_route === '' || $request_route === '/index.php') {
			if (isset($this->routes[$this->method]['/'])) return $this->routes[$this->method]['/'];
			if (isset($this->routes['*']['/'])) return $this->routes['*']['/'];
			return array();
		}
		
		$route_list = array();
		
		// Get generic routes
		if (isset($this->routes['*'])) { 
			$route_list = $this->routes['*']; 
		}
		
		// Merge routes with HTTP  request method routes
		if (isset($this->routes[$this->method])) { 
			$route_list = array_merge($route_list, $this->routes[$this->method]);
		}
		
		// Return empty if none route has found
		if (empty($route_list)) { 
			return array(); 
		}
		
		// Return match static route
		if (isset($route_list[$request_route])) {
			return $route_list[$request_route];
		} elseif (isset($route_list[$request_route.'/'])) {
			return $route_list[$request_route.'/'];
		}

		$request_route = substr($request_route, 1);
		$request_parts = explode('/', $request_route);	
		
		foreach ($route_list as $route => $route_values) {
			$route = $this->remove_last_slash(substr($route, 1));
			$route_parts = explode('/', $this->remove_last_slash($route));

			// Next if is root
			if (!$route) continue;
			// Next if route parts not equal
			if (sizeof($request_parts) !== sizeof($route_parts)) continue;
			
			// 			// Pula se parte estática for diferente.
			// 			$static = explode(':',$route);
			// 			if(substr($uri[0],0,strlen($static[0]))!==$static[0]) continue;
			
			// Get route variables
			preg_match_all('/{([\w]+)}/', $route, $params_name, PREG_PATTERN_ORDER);
			
			// Next if dont have variables in route
			if (!count($params_name[0])) continue;
			
			$route_regex = $route;
			$route_regex = str_replace('/','\/',$route_regex);
			foreach ($params_name[0] as $name) {
				$regex = '[.a-zA-Z0-9_\+\-%]';
				if (isset($route_values[$name])) $regex = $route_values[$name];
				$route_regex = str_replace($name, '('.$regex.'*)', $route_regex);
			}
			$route_regex .= '$';
			
			if (preg_match('/'.$route_regex.'/' , $request_route, $matches) === 1) {
				// Remove first value on match. (dont need this value)
				array_shift($matches);
				
				// Next if params number is not equal
				if (count($params_name[1]) != count($matches)) continue;
				
				foreach ($params_name[1] as $key => $value) {
					$this->params[$value] = $matches[$key];
				}
				
				return $route_values;
			}
		}
		
		return array();
	}
	
	
	public function __call($name, $arguments) {
		$named_route = str_replace('_path', '', $name);
		if (substr($name, -5, strlen($name)-1) === '_path') {
			return $this->url_for($named_route, $arguments);
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
		if (isset($this->routes['*']) && $path = $this->find_named_route($this->routes['*'], $named_route, $params)) {
		} elseif (isset($this->routes['get']) && $path = $this->find_named_route($this->routes['get'], $named_route, $params)) {
		} elseif (isset($this->routes['put']) && $path = $this->find_named_route($this->routes['put'], $named_route, $params)) {
		} elseif (isset($this->routes['post']) && $path = $this->find_named_route($this->routes['post'], $named_route, $params)){}
		if (!$path) trigger_error('Named route for '.$named_route.' doenst exist', E_USER_ERROR);
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
	public function find_named_route($route_list, $named_route, $params = array()) {
		$path = false;
		
		foreach ($route_list as $url => $route) {
			
			if (isset($route['as']) && $route['as'] == $named_route ) {
				
				// Get route variables
				preg_match_all('/{([\w]+)}/', $url, $route_params, PREG_PATTERN_ORDER);
				$route_params = $route_params[0];
				
				// Continue if not have variables
				if (!count($route_params)) { $path = $url; continue; }
				
				if (count($params) != count($route_params)) {
					die('Named route for '.$named_route.' expects '.count($route_params).' params not '.count($params).'.'); 
				}
				 
				$route_regex = str_replace('/', '\/', $url);
				
				$params_total_cache = count($route_params);
				for ($i=0; $i < $params_total_cache; $i++) { 
					$route_regex = preg_replace('/'.$route_params[$i].'/', $params[$i], $route_regex, 1);
				}
				
				$path = $route_regex;			
			}
			
		}
		
		return $path;
	}
	
	/**
	 * Redirect to external url, with http header 302 
	 *
	 * @param string $location URL of the redirect location
	 * @param code $code HTTP status code to be sent with the header
	 * @param bool $exit To end the application
	 * @return void
	 */
	public function redirect($location, $code = 302, $exit = true) {
		header("Location: $location", true, $code);
		if ($exit) die;
	}
	
	/**
	 * Remove the slash(/) from the last char.
	 *
	 * @param string $string
	 * @return string
	 */
	protected function remove_last_slash($string) {
		
		if ($string[strlen($string)-1] === '/') {
			$string = substr($string, 0, -1);
		}
		
		return $string;
	}
	
}
?>
