<?
/**
 * Módulo para Cinema
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleCine extends AppModel {

	public $table = "cines";
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
