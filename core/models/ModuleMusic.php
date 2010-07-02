<?
/**
 * Módulo para Música
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleMusic extends AppModel {

	public $table = "musics";
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
