<?
/**
 * Módulo Custom Selector
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleCustomSelector extends AppModel {
	
	/**
	 * Adiciona os comportamentos do módulo
	 *
	 * @access public
	 * @return void
	 */
	 
	public $table = "core_select_options_records";
	
	/**
	 * Adiciona os comportamentos do módulo
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		//$this->imports('Page');
		//$this->imports('Selector');
		//$this->imports('Tag');
		//$this->imports('Comment');
	}
}
?>
