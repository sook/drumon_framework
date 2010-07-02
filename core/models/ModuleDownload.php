<?
/**
 * Módulo para Download
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleDownload extends AppModel {

	public $table = "downloads";
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
