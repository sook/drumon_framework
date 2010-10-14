<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Módulo Custom Selector.
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleCustomSelector extends AppModel {
	
	/**
	 * Nome da tabela.
	 *
	 * @access public
	 * @var string
	 */
	 
	public $table = "core_select_options_records";
	
	/**
	 * Adiciona os comportamentos do módulo.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
}
?>
