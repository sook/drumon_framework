<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Módulo para enquete
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModulePoll extends AppModel {
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo módulo
	 *
	 * @access public
	 * @var string
	 */
	public $table = "polls";
	
	/** 
	 * Armazena uma lista de funcionalidades que o módulo irá dispor
	 *
	 * @access protected
	 * @var array
	 */
	protected $uses = array('trash','status');
	
	/**
	 * Adiciona os comportamentos do módulo
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->imports('Selector');
	}
	
	/**
	 * Busca as respostas da enquete específica
	 *
	 * @access public
	 * @param array $params
	 * @return array
	 */
	public function findAll($params = array()) {
		$polls = parent::findAll($params);
		if(!$polls) return false;
		
		foreach ($polls as $key => $value) {
			$s = $this->connection->find("SELECT * FROM polls_responses WHERE polls_id = ".$polls[$key]['id']." ");
			$polls[$key]['responses'] = $s;
		}
		return $polls;
	}
}
?>
