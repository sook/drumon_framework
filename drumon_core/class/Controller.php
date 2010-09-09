<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe abstrata que fornece suporte a classe base de controlador.
 *
 * @package class
 * @abstract
 * @author Sook contato@sook.com.br
 */
abstract class Controller {
	
	/** 
	 * Referência da variável com os dados de internacionalização.
	 *
	 * @access private
	 * @var string
	 */
	private	$i18n = null;
	
	/** 
	 * Array com as informações pertencentes a aplicação.
	 *
	 * @access private
	 * @var array
	 */
	private	$config;
	
	/** 
	 * Arquivo de template a ser usado pelo controlador.
	 *
	 * @access protected
	 * @var string
	 */
	protected $template;
	
	/** 
	 * Arquivo de layout a ser usado pelo controlador.
	 *
	 * @access protected
	 * @var string
	 */
	protected $layout = "default";
	
	/** 
	 * Arquivos helper a serem usados pelo controlador.
	 *
	 * @access protected
	 * @var array
	 */
	protected $helpers = array();
	
	/** 
	 * Indica se a página será renderizada.
	 *
	 * @access protected
	 * @var boolean
	 */
	protected $render = true;
	
	/** 
	 * Contém os parâmetros passados na requisição HTTP (GET e POST).
	 *
	 * @access protected
	 * @var array
	 */
	protected $params = array();

	/**
	 * Instancia um novo template com as configurações, parâmetros e idioma padrões.
	 *
	 * @access public
	 * @param object $request - Instância do Request Handler.
	 * @param array $i18n - Referência da variável com os dados de internacionalização.
	 */
	public function __construct($request,$i18n){
		$this->i18n = $i18n;
		$this->params = $request->params;
		$this->template = new Template();
		$this->request = $request;
	}

	/**
	 * Executada antes de qualquer ação no controlador.
	 *
	 * @access public
	 * @return void
	 */
	public function beforeFilter() {}

	/**
	 * Executada posteriormente a qualquer ação no controlador.
	 *
	 * @access public
	 * @return void
	 */
	public function afterFilter() {}

	/**
	 * Adiciona valores às chaves.
	 *
	 * @access public
	 * @param String $key - Chave conteiner que se tornará uma variável no template.
	 * @param Mixed $value - Valores que sernao adicionados a chave no template.
	 * @return void
	 */
	public function add($key, $value){
		$this->template->add($key, $value);
	}

	/**
	 * Renderiza as Views.
	 *
	 * @access public
	 * @param string $view - View a ser renderizada.
	 * @return void
	 */
	public function render($view){
		$this->template->params = $this->params;
		$this->loadHelpers();
		$view = $view[0] == '/' ? substr($view, 1) : '/views/'.strtolower($this->request->controller_name).'/'.$view;

		$content = $this->template->renderPage(ROOT.$view.".php");

		// Para não redenrizar layout.
		// Setar no controller: var $layout = null;
		if(!empty($this->layout)){
			$this->add('content',$content);
			$content = $this->template->fetch(ROOT.'/views/layouts/'.$this->layout.'.php');
		}
		echo $content;

		if (BENCHMARK){
			echo '<style type="text/css">
						div.cms_debug{
						background-color: white;
						position: fixed;
						bottom:0;
						-moz-box-shadow:0 -1px 4px #000;
						box-shadow:0 -1px 4px #000;
						-webkit-box-shadow:0 -1px 4px #000;
						padding: 2px 4px 0 4px;
						left:10px;
						opacity:0.3;
					}
					div.cms_debug:hover{
						opacity:1;
					}
				</style>';
			Benchmark::stop('Load Time');
			echo '<div class="cms_debug">';
			foreach (Benchmark::getTotals() as $total) {
				echo $total.'<br>';
				}
				echo '</div>';
			}
			die(); // Para garantir e não chamar 2 render.
		}

	/**
	 * Redireciona para url desejada.
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
	 * Passa valor de status para o cabeçalho.
	 *
	 * @access public
	 * @param string $status - Status de cabeçalho.
	 * @return void
	 */
	function header($status) {
		header($status);
	}

	/**
	 * Executa ação, os seus filtros e redenriza a view.
	 *
	 * @access public
	 * @param string $action - Ação a ser executada.
	 * @return void
	 */
	public function execute($action) {
		$this->beforeFilter();
		$this->$action();
		$this->afterFilter();

		$this->render($action);
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
	private function loadHelpers() {
		// Helpers existentes no core.
		$core_helpers = array('Date','Html','Image','Text','Paginate');
		
		$default_helpers = (DEFAULT_HELPERS === '') ? array() : explode(',',DEFAULT_HELPERS);
		$this->helpers = array_merge($this->helpers, $default_helpers);
		// Adiciona os helpers na view.
		foreach ($this->helpers as $helper) {
			$local = in_array($helper, $core_helpers) ? CORE : ROOT;
			require $local."/helpers/".$helper."Helper.php";
			$class = $helper.'Helper';
			$this->add(strtolower($helper), new $class($this->i18n));
		}

		// Adiciona os helpers requeridos em outros helpers.
		foreach ($this->helpers as $helper) {
			$helper = $this->template->get(strtolower($helper));
			foreach ($helper->uses as $name) {
				$name = strtolower($name);
				$helper->$name = $this->template->get($name);
			}
		}
	}
}?>
