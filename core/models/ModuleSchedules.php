<?
/**
 * Módulo para schedules
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleSchedules extends AppModel {

	public $table = "schedules";
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
