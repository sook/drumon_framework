<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Core Application for Drumon Framework
 *
 * @package class
 */
class App {
	
	/**
	 * Application object instance
	 *
	 * @var App
	 */
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
	 * Cache all translations for application
	 *
	 * @var string
	 */
	public $translations_cache = array();
	
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
	 * Run Application
	 *
	 * @return void
	 */
	public static function run() {
		// Get application object
		$app = self::get_instance();		
		
		// Default configurations
		$app->config['app_domain']			 = App::remove_last_slash('http://' . $_SERVER['SERVER_NAME'] . dirname($_SERVER['SCRIPT_NAME']));
		$app->config['stylesheets_path'] = $app->config['app_domain'] . '/public/stylesheets/';
		$app->config['javascripts_path'] = $app->config['app_domain'] . '/public/javascripts/';
		$app->config['images_path']			 = $app->config['app_domain'] . '/public/images/';
		
		$route = array();
		$route['404'] = array('RequestError::error_404');
		$route['401'] = array('RequestError::error_401');
		
		include(APP_PATH.'/config/routes.php');
		include(APP_PATH.'/config/application.php');
		include(APP_PATH.'/config/enviroments/'.$app->config['env'] . '.php');
		
		// Set most used configurations, for rapid access.
		define('APP_DOMAIN',			 $app->config['app_domain']);
		define('STYLESHEETS_PATH', $app->config['stylesheets_path']);
		define('JAVASCRIPTS_PATH', $app->config['javascripts_path']);
		define('IMAGES_PATH',			 $app->config['images_path']);
		define('PLUGINS_PATH',		 APP_PATH.'/vendor/plugins');
		define('LANGUAGE',			   $app->config['language']);
		define('JS_FRAMEWORK',     $app->config['js_framework']);
		
		// Load application plugins
		foreach ($app->plugins as $plugin) {
			require_once(APP_PATH.'/vendor/plugins/' . $plugin . '/init.php');
		}
		
		// Fire on_init event
		$app->fire_event('on_init');
		
		// Required files for Drumon
		include(CORE_PATH . '/class/request.php');
		include(CORE_PATH . '/class/response.php');
		include(CORE_PATH . '/class/helper.php');
		include(CORE_PATH . '/class/view.php');
		include(CORE_PATH . '/class/controller.php');
		include(APP_PATH  . '/app/controllers/app_controller.php');
		
		// Token protection against CSFR
		define('REQUEST_TOKEN', $app->create_request_token());

		// Initialize request
		$request = new Request($route, APP_PATH);
		$request->validate();
		// Proccess controller and action
		$app->proccess_controller($request);
	}
	
	/**
	 * Proccess controller, action and show response
	 *
	 * @param obj $request 
	 * @return void
	 */
	public function proccess_controller($request) {
		$core_controllers = array('RequestError' => CORE_PATH.'/class/' );
		$controller_path = (isset($core_controllers[$request->controller_name])) ? $core_controllers[$request->controller_name] : APP_PATH.'/app/controllers/';
		
		$real_class_name = $request->controller_name.'Controller'; // ex. HomeController || Admin_HomeController
		require_once($controller_path.App::to_underscore(str_replace('_', '/', $real_class_name)).'.php');
		$controller = new $real_class_name($this, $request, new Response(), new View());
		
		$response = $controller->process();
		$this->fire_event('on_complete');
		
		echo $response;
	}
	
	/**
	 * Add helpers to use on application
	 *
	 * @param string|array $helpers 
	 * @return void
	 */
	public function add_helpers($helpers_names, $custom_paths = null) {
		// List of core Helpers
		$core_helpers = array('date','html','image','text','url','movie');

		$helpers = array();
		$helpers_names = is_array($helpers_names) ? $helpers_names : array($helpers_names);
		foreach ($helpers_names as $helper_name) {
			$helper_name = strtolower(trim($helper_name));
			$local = in_array($helper_name, $core_helpers) ? CORE_PATH . '/helpers' : APP_PATH . '/app/helpers';
			if ($custom_paths) {
				$local = $custom_paths;
			}
			$helpers[$helper_name] = $local . "/" . $helper_name . "_helper.php";
		}
		
		$this->helpers = array_merge($this->helpers, $helpers);
	}
	
	/**
	 * Add plugins to use on application
	 *
	 * @param string|array $plugins 
	 * @return void
	 */
	public function add_plugins($plugins) {
		$plugins = is_array($plugins) ? $plugins : array($plugins);
		$this->plugins = array_merge($this->plugins, $plugins);
	}
	
	/**
	 * Add event to application
	 *
	 * @param string $name 
	 * @param string|array $callback 
	 * @return void
	 */
	public function add_event($name, $callback) {
		$this->event_list[$name][] = $callback;
	}
	
	/**
	 * Fire added events on application
	 *
	 * @param string $name 
	 * @param array $params 
	 * @return void
	 */
	public function fire_event($name, $params = array()) {
		if (array_key_exists($name, $this->event_list)) {
			foreach ($this->event_list[$name] as $callback) {
				call_user_func_array($callback, &$params);
			}
		}
	}
	
	/**
	 * Set application configuration
	 *
	 * @param string $name 
	 * @param mix $value 
	 * @return void
	 */
	public static function set_config($name, $value) {
		$app = self::get_instance();
		return $app->config[$name] = $value;
	}
	
	/**
	 * Get application configuration
	 *
	 * @param string $name 
	 * @return mix
	 */
	public static function get_config($name) {
		$app = self::get_instance();
		return $app->config[$name];
	}
	
	
	/**
	 * Generate unique token for CSRF protection
	 *
	 * @return string
	 * 
	 */
	public function create_request_token() {
		$token	= dechex(mt_rand());
		$hash		= sha1($this->config['app_secret'] . APP_DOMAIN . '-' . $token);
		return $token . '-' . $hash;
	}
	
	
	/**
	 * Check if can block request against CSRF
	 *
	 * @param object $request 
	 * @return bool
	 */
	public function block_request($request) {
		
		$unauthorized = false;
		
		if ($request->method != 'get') {
			$unauthorized = true;

			if (!empty($request->params['_token'])) {
				$parts = explode('-', $request->params['_token']);

				if (count($parts) == 2) {
					list($token, $hash) = $parts;
					if ($hash == sha1($this->config['app_secret'] . APP_DOMAIN . '-' . $token)) {
						$unauthorized = false;
					}
				}
			}
		}
		
		return $unauthorized;
	}
	
	/**
	 * Turn CamelCaseWords to underscore_words
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
	 * Turn underscore_words to CamelCaseWords
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
	 * Convert string to slug
	 *
	 * @access public
	 * @param string $text - String to convert.
	 * @param string $space - Character used beetween words (default: -).
	 * @return string
	 */
	public function to_slug($text, $space = "-") {
		$text = trim($text);

		$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
		$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
		$text = str_replace($search, $replace, $text);

		if (function_exists('iconv')) {
			$text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
		}

		$text = preg_replace("/[^a-zA-Z0-9 ".$space."]/", "", $text);
		$text = str_replace(" ", $space, $text);
		$text = preg_replace("/".$space."{2,}/",$space,$text);
		$text = strtolower($text);
		return $text;
	}
	
	/**
	 * Remove NULL and empty values from array
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
	
	/**
	 * Convert array to html helper select format
	 *
	 * @param string $list 
	 * @param string $key 
	 * @param string $value 
	 * @return array
	 */
	public static function to_select($list, $key, $value) {
		$result = array();
		foreach ($list as $item) {
			$result[$item[$key]] = $item[$value];
		}
		return $result;
	}
	
	/**
	 * Remove the slash(/) from the last char.
	 *
	 * @access public
	 * @param string $value
	 * @return string
	 */
	public static function remove_last_slash($value) {
		if($value[strlen($value)-1] === '/') {
			$value = substr($value, 0, -1);
		}
		return $value;
	}
}


/**
 * Translate text to defined language using lazy loading.
 *
 * @param string $key 
 * @param array $options
 * @return string
 */
function t($key, $options = array()) {
	// Merge options with defaults
	$options = array_merge(array('from' => 'application'), $options);
	
	// Load translation file
	$app = App::get_instance();
	if (!isset($app->translations_cache[$options['from']])) {
		$app->translations_cache[$options['from']] = include(APP_PATH.'/config/locales/'.LANGUAGE.'/'.$options['from'].'.php');
	}
	
	// Setup important variables
	$translations = $app->translations_cache[$options['from']];
	$keys = explode('.',$key);
	$end_key = end($keys);
	$text = $options['from'].'.'.$key;
	
	// Get last array key values
	foreach ($keys as $key) {
		$is_end = $key == $end_key;
		if (isset($translations[$key])) {
			if (is_array($translations[$key]) && !$is_end) {
				$translations = $translations[$key];
			}
		}
	}
	
	// Get correct pluralization translation word
	if (isset($options['count'])) {
		if (isset($translations[$end_key][$options['count']])) {
			$text = $translations[$end_key][$options['count']];
		} elseif (isset($translations[$end_key]['*'])) {
			$text = str_replace("{count}", $options['count'], $translations[$end_key]['*']);
		} else {
			$text .= '.*';
		}
	} else {
		// Get translation value
		$text = isset($translations[$end_key]) ? $translations[$end_key] : $text;
	}
	
	// Replace variables words
	foreach ($options as $key => $value) {
		if ($key != 'from' && $key != 'count') {
			$text = str_replace('{'.$key.'}', $value, $text);
		}
	}

	return $text;
}
?>