<?
/**
 * Módulo para galeria de fotos 
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModulePhotoGallery extends AppModel {
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo módulo
	 *
	 * @access public
	 * @name $table
	 */
	public $table = "photo_galleries";
	
	/** 
	 * Armazena o nome do módulo
	 *
	 * @access public
	 * @name $table
	 */
	public $name = "PhotoGallery";
	
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
	}
}
?>
