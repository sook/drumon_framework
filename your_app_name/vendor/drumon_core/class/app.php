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
	 * Guarda as traduções requesitadas pelo usuário.
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
	
	
	public static function run() {
		// Obtem a instancia da aplicação
		$app = self::get_instance();		
		
		// Configurações padrões do framework
		$app->config['app_domain']			 = App::strip_slash('http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']));
		$app->config['stylesheets_path'] = $app->config['app_domain'].'/public/stylesheets/';
		$app->config['javascripts_path'] = $app->config['app_domain'].'/public/javascripts/';
		$app->config['images_path']			 = $app->config['app_domain'].'/public/images/';
		
		$route = array();
		$route['400'] = '404.html';
		$route['401'] = '401.html';
		$route['403'] = '404.html';
		$route['404'] = '404.html';
		
		include(APP_PATH.'/config/routes.php');
		include(APP_PATH.'/config/application.php');
		include(APP_PATH.'/config/enviroments/'.$app->config['env'].'.php');
		
		// Seta as constantes mais utilizadas
		define('APP_DOMAIN',			 $app->config['app_domain']);
		define('STYLESHEETS_PATH', $app->config['stylesheets_path']);
		define('JAVASCRIPTS_PATH', $app->config['javascripts_path']);
		define('IMAGES_PATH',			 $app->config['images_path']);
		define('PLUGINS_PATH',			     APP_PATH.'/vendor/plugins');
		define('LANGUAGE',			   $app->config['language']);
		define('JS_FRAMEWORK',     $app->config['js_framework']);
		
		// Carrega plugins
		foreach ($app->plugins as $plugin) {
			require_once(APP_PATH.'/vendor/plugins/'.$plugin.'/init.php');
		}
		
		// Dispara o evento de inicialização do Framework
		$app->fire_event('on_init');
		
		// Inclui arquivos requeridos pelo Drumon
		include(CORE.'/class/request_handler.php');
		include(CORE.'/class/helper.php');
		include(CORE.'/class/view.php');
		include(CORE.'/class/controller.php');
		include(APP_PATH.'/app/controllers/app_controller.php');


		/**
		 * Inicia o sistema de roteamento.
		 */
		$request = new RequestHandler($route);
		
		// Se a rota existe.
		if ($request->valid()) {
			$status_code = 200;
			// Token de proteção contra CSFR
			define('REQUEST_TOKEN', $app->create_request_token());
			
			// Verifica se é para bloquear
			if($app->block_csrf_protection($request)) {
				// se bloquear renderiza pagina de erro
				$status_code = 401;
				if (is_array($route['401'])) {
					$request->controller_name = $route['401'][0];
					$request->action_name = $route['401'][1];
					$controller = $app->load_controller($request);
					$html = $controller->execute_view();
				} else {
					$html = file_get_contents(APP_PATH.'/public/'.$route['401']);
				}
			} else {
				// se não renderiza nornalmente
				$controller = $app->load_controller($request);
				$html = $controller->execute_view();
			}
		} else {
			// Página não encontrada.
			$status_code = 404;
			if (is_array($route['404'])) {
				$request->controller_name = $route['404'][0];
				$request->action_name = $route['404'][1];
				$controller = $app->load_controller($request);
				$html = $controller->execute_view();
			} else {
				$html = file_get_contents(APP_PATH.'/public/'.$route['404']);
			}
		}
		
		// Lista de Http Status básicos
		$status_code_list = array(
			200 => '200 OK',
			304 => '304 Not Modified',
			400 => '400 Bad Request',
			401 => '401 Unauthorized',
			403 => '403 Forbidden',
			404 => '404 Not Found',
			500 => '500 Internal Server Error'
		);
		
		// Seta o http status
		$status_code = !empty($controller->http_status_code) ? $controller->http_status_code : $status_code;
		header($_SERVER["SERVER_PROTOCOL"]." ".$status_code_list[$status_code]);
		
		// Imprime o conteúdo
		$app->show_content($html);
	}
	
	/**
	 * Adiciona helpers que serão usados na aplicação
	 *
	 * @param string|array $helpers 
	 * @return void
	 */
	public function add_helpers($helpers_names, $custom_paths = null) {
		// Helpers existentes no core.
		$core_helpers = array('date','html','image','text','url','movie');

		$helpers = array();
		$helpers_names = is_array($helpers_names) ? $helpers_names : array($helpers_names);
		foreach ($helpers_names as $helper_name) {
			$helper_name = strtolower(trim($helper_name));
			$local = in_array($helper_name, $core_helpers) ? CORE.'/helpers' : APP_PATH.'/app/helpers';
			if ($custom_paths) {
				$local = $custom_paths;
			}
			$helpers[$helper_name] = $local."/".$helper_name."_helper.php";
		}
		
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
	 * @param array $params 
	 * @return void
	 */
	public function fire_event($name, $params = array()) {
		if(array_key_exists($name,$this->event_list)){
			foreach ($this->event_list[$name] as $callback) {
				call_user_func_array($callback, &$params);
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
	
	/**
	 * Imprime o conteúdo do site e dispara os eventos.
	 *
	 * @param string $content 
	 * @return void
	 */
	public function show_content($content) {
		$this->fire_event('before_show', array('content' => &$content));
		echo $content;
		$this->fire_event('on_complete', array('content' => $content));
	}
	
	
	public function load_controller($request) {
		$real_class_name = $request->controller_name.'Controller'; // ex. HomeController || Admin_HomeController
		
		// Inclui o controlador.
		include(APP_PATH.'/app/controllers/'.App::to_underscore(str_replace('_','/',$real_class_name)).'.php');
		
		// Inicia o controlador e chama a ação.
		$controller = new $real_class_name($this, $request, new View());
		$controller->execute_action();
		return $controller;
	}
	
	
	/**
	 * Gera token única para a requisição.
	 *
	 * @return string
	 * 
	 */
	public function create_request_token() {
		$token	= dechex(mt_rand());
		$hash		= sha1($this->config['app_secret'].APP_DOMAIN.'-'.$token);
		return $token.'-'.$hash;
	}
	
	
	/**
	 * Protege contra ataques do tipo CSRF.
	 *
	 * @param object $request 
	 * 
	 */
	public function block_csrf_protection($request) {
		
		$unauthorized = false;
		
		if ($request->method != 'get') {
			$unauthorized = true;

			if (!empty($request->params['_token'])) {
				$parts = explode('-',$request->params['_token']);

				if (count($parts) == 2) {
					list($token, $hash) = $parts;
					if ($hash == sha1($this->config['app_secret'].APP_DOMAIN.'-'.$token)) {
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
	 * Converte um texto para o formato de slug, retirando os acentos e espaços.
	 *
	 * @access public
	 * @param string $text - Texto a ser formatado.
	 * @param string $space - Caractere usado no lugar do espaço (default: -).
	 * @return string -  Texto formatado.
	 */
	public function to_slug($text, $space = "-") {
		$text = trim($text);

		$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
		$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
		$text = str_replace($search, $replace, $text);

		if (function_exists('iconv')) {
			$text = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
		}

		$text = preg_replace("/[^a-zA-Z0-9 ".$space."]/", "", $text);
		$text = str_replace(" ", $space, $text);
		$text = preg_replace("/".$space."{2,}/",$space,$text);
		$text = strtolower($text);
		return $text;
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
	 * @param string $str - String com (/) a ser alterada.
	 * @return string - String sem a barra (/).
	 */
	public static function strip_slash($str) {
		if($str[strlen($str)-1]==='/') {
			$str = substr($str,0,-1);
		}
		return $str;
	}
}


/**
 * Traduz um texto com o sistema de internacionalização.
 *
 * @param string $text 
 * @return string
 */
function t($text) {
	$app = App::get_instance();
	
	$parts = explode('.',$text);
	$file_name = 'application';
	
	if(count($parts) > 1) {
		$file_name = $parts[0];
		$text = $parts[1];
	}
	
	if (!isset($app->translations_cache[$file_name])) {
		$app->translations_cache[$file_name] = include(APP_PATH.'/config/locales/'.LANGUAGE.'/'.$file_name.'.php');
	}

	$text = (isset($app->translations_cache[$file_name][$text])) ? $app->translations_cache[$file_name][$text] : implode('.',$parts);
	return $text;
}
?>
