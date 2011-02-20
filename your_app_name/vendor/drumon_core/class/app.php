<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe com métodos essências para funcionamento do Drumon Framework
 *
 * @package class
 */
class App {
	
	private static $instance;
	
	/**
	 * Events list to fire
	 *
	 * @var array
	 */
	private $event_list = array();
	
	/**
	 * Drumon configurations
	 *
	 * @var array
	 */
	public $config = array();
	
	/**
	 * Drumon plugins
	 *
	 * @var array
	 */
	public $plugins = array();
	
	/**
	 * Drumon helpers
	 *
	 * @var array
	 */
	public $helpers = array();
	
	/**
	 * Block object initialization
	 *
	 */
	private function __construct() {}
	
	/**
	 * Throws an exception on clone
	 *
	 * @throws Exception
	 */
	public final function __clone() { 
		throw new BadMethodCallException("Clone is not allowed"); 
	}
	
	/**
	 * Get singleton instance
	 *
	 * @return Event
	 */
	public static function get_instance() {
		if (!isset(self::$instance)) {
    		self::$instance = new App();
		}
		return self::$instance;
	}
	
	/**
	 * Adiciona helpers que serão usados na aplicação
	 *
	 * @param string|array $helpers 
	 * @return void
	 */
	public function add_helpers($helpers) {
		$helpers = is_array($helpers) ? $helpers : array($helpers);
		$this->helpers = array_merge($this->helpers, $helpers);
	}
	
	/**
	 * Adiciona plugins que serão usados na aplicação
	 *
	 * @param string|array $plugins 
	 * @return void
	 */
	public function add_plugins($plugins) {
		$plugins = is_array($plugins) ? $plugins : array($plugins);
		$this->plugins = array_merge($this->plugins, $plugins);
	}
	
	/**
	 * Adiciona evento na aplicação
	 *
	 * @param string $name 
	 * @param string|array $callback 
	 * @return void
	 */
	public function add_event($name, $callback) {
		$this->event_list[$name][] = $callback;
	}
	
	/**
	 * Dispara evento na aplicação
	 *
	 * @param string $name 
	 * @param string $params 
	 * @return void
	 */
	public function fire_event($name, $params = null) {
		if(array_key_exists($name,$this->event_list)){
			foreach ($this->event_list[$name] as $callback) {
				call_user_func_array($callback, array(&$params));
			}
		}
	}
	
	/**
	 * Adiciona variável de configuração da app
	 *
	 * @param string $name 
	 * @param mix $value 
	 * @return void
	 */
	public static function add_config($name, $value) {
		$app = self::get_instance();
		return $app->config[$name] = $value;
	}
	
	/**
	 * Retorna a variável setada na app
	 *
	 * @param string $name 
	 * @return mix
	 */
	public static function get_config($name) {
		$app = self::get_instance();
		return $app->config[$name];
	}
	
	
	public function execute_controller($app, $request) {
		// Variáveis básicas para o controlador.
		$path = ROOT.'/app/controllers/';
		$real_class_name = $request->controller_name.'Controller'; // ex. HomeController || Admin_HomeController
		
		// Quebra em partes para ver se possui namespace
		$class_parts = explode('_', $request->controller_name);
		
		// Se tem namespace
		if (count($class_parts) > 1) {
			$class_name = array_pop($class_parts);
			$namespaces = implode('/', $class_parts);
			$path .= App::to_underscore($namespaces).'/';
			$file_name = App::to_underscore($class_name.'Controller');
		}else{
			$namespaces = null;
			$file_name = App::to_underscore($real_class_name);
			$class_name = $request->controller_name;
		}
		
		// Inclui o controlador.
		include($path.$file_name.'.php');
		
		// Inicia o controlador e chama a ação.
		$controller = new $real_class_name($app, $request, new Template(), $namespaces, $class_name);
		return $controller->execute($request->action_name);
	}
	
	
	/**
	 * Gera token única para a requisição.
	 *
	 * @return string
	 * 
	 */
	public function create_request_token() {
		$token  = dechex(mt_rand());
		$hash   = sha1(APP_SECRET.APP_DOMAIN.'-'.$token);
		return $token.'-'.$hash;
	}
	
	
	/**
	 * Protege contra ataques do tipo CSRF.
	 *
	 * @param object $request 
	 * 
	 */
	public static function block_csrf_protection($request) {
		
		$unauthorized = false;
		
		if ($request->method != 'get') {
			$unauthorized = true;

			if (!empty($request->params['_token'])) {
				$parts = explode('-',$request->params['_token']);

				if (count($parts) == 2) {
			    list($token, $hash) = $parts;
			    if ($hash == sha1(APP_SECRET.APP_DOMAIN.'-'.$token)) {
						$unauthorized = false;
					}
				}
			}
		}
		
		return $unauthorized;
	}
	
	/**
	 * Transforma palavrasEmCamelCase para palavras_em_underscore
	 *
	 * @param string $camelCasedWord 
	 * @return string
	 */
	public static function to_underscore($camelCasedWord) {
		$result = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
		$result = str_replace(' ', '_', $result);
		return $result;
	}
	
	/**
	 * Transforma palavras_em_underscore em PalavrasEmCamelCase
	 *
	 * @param string $lowerCaseAndUnderscoredWord 
	 * @return string
	 */
	public static function to_camelcase($lowerCaseAndUnderscoredWord) {
		$lowerCaseAndUnderscoredWord = ucwords(str_replace('_', ' ', $lowerCaseAndUnderscoredWord));
		$result = str_replace(' ', '', $lowerCaseAndUnderscoredWord);
		return $result;
	}
	
	/**
	 * Remove valores vazios e nulos do array.
	 *
	 * @param string $array 
	 * @return array
	 */
	public static function array_clean($array) {
		$clean_array = array();
		foreach ($array as $value) {
			if (!empty($value)) {
				$clean_array[] = $value;
			}
		}
		return $clean_array;
	}
}
?>