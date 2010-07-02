<?
/**
 * Módulo para vídeo
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleVideo extends AppModel {

	public $table = "videos";
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
