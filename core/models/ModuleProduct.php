<?
/**
 * Módulo para produto
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleProduct extends AppModel {

	public $table = "products";
	protected $uses = array('trash','status');

	public function __construct() {
		parent::__construct();
		$this->imports('Page');
		$this->imports('Selector');
		$this->imports('Tag');
		//$this->imports('Comment');
	}
}
?>
