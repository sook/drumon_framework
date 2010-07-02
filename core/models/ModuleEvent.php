<?
/**
 * Módulo para Evento
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleEvent extends AppModel {

	public $table = "events";
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
