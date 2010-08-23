<?
/**
 * Módulo para Newsletter
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleNewsletter extends AppModel {
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo módulo
	 *
	 * @access public
	 * @var string
	 */
	public $table = "newsletters";
	
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
