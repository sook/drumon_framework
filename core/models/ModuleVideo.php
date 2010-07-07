<?
/**
 * Módulo para vídeo
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleVideo extends AppModel {
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo módulo
	 *
	 * @access public
	 * @name $table
	 */
	public $table = "videos";
	
	/** 
	 * Armazena uma lista de funcionalidades que o módulo irá dispor
	 *
	 * @access protected
	 * @name $uses
	 */
	protected $uses = array('trash','status');
	
	/**
	 * Adiciona os comportamentos do módulo
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->imports('Page');
		$this->imports('Selector');
		$this->imports('Tag');
		$this->imports('Comment');
	}
}
?>
