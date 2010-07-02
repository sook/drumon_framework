<?
/**
 * Módulo para theater
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleTheater extends AppModel {

	public $table = "theaters";
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
