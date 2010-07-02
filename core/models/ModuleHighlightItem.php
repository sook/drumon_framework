<?
/**
 * Módulo para 
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleHighlightItem extends AppModel {

	public $table = "highlight_items";
	protected $uses = array('trash','status');

	public function __construct() {
		parent::__construct();
		//$this->imports('Page');
		$this->imports('Selector');
		//$this->imports('Tag');
		//$this->imports('Comment');
	}
}
?>
