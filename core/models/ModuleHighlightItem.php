<?
/**
 * Módulo para 
 *
 * @author Sook contato@sook.com.br.
 * @package models
 */
class ModuleHighlightItem extends AppModel {
	
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo módulo.
	 *
	 * @access public
	 * @var string
	 */
	public $table = "highlight_items";
	
	/** 
	 * Armazena uma lista de funcionalidades que o módulo irá dispor.
	 *
	 * @access protected
	 * @var array
	 */
	protected $uses = array('trash','status');
	
	/**
	 * Adiciona os comportamentos do módulo.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		//$this->imports('Page');
		$this->imports('Selector');
		//$this->imports('Tag');
		//$this->imports('Comment');
	}
}
?>
