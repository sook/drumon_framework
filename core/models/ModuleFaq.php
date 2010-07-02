<?
/**
 * Módulo para faq
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleFaq extends AppModel {

	public $table = "faqs";
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
