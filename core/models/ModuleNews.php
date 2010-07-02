<?
/**
 * Módulo para Notícias
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleNews extends AppModel {
	
	// Nome da tabela do modelo.
	public $table = "news";
	// Nome do modelo.
	public $name = "New";
	
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
