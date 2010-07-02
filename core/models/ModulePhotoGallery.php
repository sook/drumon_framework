<?
/**
 * Módulo para galeria de fotos 
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModulePhotoGallery extends AppModel {

	public $table = "photo_galleries";
	
	public $name = "PhotoGallery";
	
	protected $uses = array('trash','status');

	public function __construct() {
		parent::__construct();
		$this->imports('Page');
		$this->imports('Selector');
		$this->imports('Tag');
	}
}
?>
