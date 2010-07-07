<?
/**
 * Módulo para atração de evento
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleEventAttraction extends AppModel {
	
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo módulo
	 *
	 * @access public
	 * @name $table
	 */
	public $table = "events_attractions";

	/**
	 * Adiciona os comportamentos do módulo
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
}
?>
