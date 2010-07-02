<?
/**
 * Módulo para galeria artística
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleArtisticGallery extends AppModel {

	public $table = "artistic_galleries";
	protected $uses = array('trash','status');

	public function __construct() {
		parent::__construct();
		$this->imports('Page');
		$this->imports('Selector');
		$this->imports('Tag');
		$this->imports('Comment');
	}
}
?>
