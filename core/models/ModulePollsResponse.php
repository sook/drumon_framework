<?
/**
 * Módulo para resposta de enquete
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModulePollsResponse extends AppModel {
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo módulo
	 *
	 * @access public
	 * @var string
	 */
	public $table = "polls_responses";
	
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
