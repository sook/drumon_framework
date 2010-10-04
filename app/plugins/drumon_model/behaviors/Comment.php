<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe para comentários dos models.
 * @author Sook contato@sook.com.br
 * @package models
 * @subpackage behaviors
 */
class Comment extends ModelBehavior {
	/**
	 * Procura comentários específicos pelo id.
	 *
	 * @access public
	 * @param string $id - ID a ser utilizado pela cláusula WHERE.
	 * @return array - Lista de dados retornados pela query.
	 */
	public function findComments($id) {
		$recordType = "Modules::".get_class($this->model);

		$params = array();
		$params['where'] = "record_type = '".$recordType."' AND record_id = ".$id." AND published = 1";
		$params['order'] = "id ASC";

		$sql = 'SELECT * FROM core_comments  WHERE record_type = "'.$recordType.'" AND record_id = '.$id.' AND published = 1 ORDER BY id ASC';
		return $this->model->query($sql);
	}

	/**
	 * Retorna a quantidade de comentários.
	 *
	 * @access public
	 * @param string $id - ID a ser utilizado pela cláusula WHERE.
	 * @return integer - Total de comentários.
	 */
	public function countComments($id) {
		$recordType = "Modules::".get_class($this->model);
		$sql = 'SELECT COUNT(*) as total FROM core_comments WHERE record_type = "'.$recordType.'" AND record_id = '.$id.' AND published = 1';
		$result = $this->model->query($sql);
		return $result[0]['total'];
	}

	/**
	 * Salva Comentários.
	 *
	 * @access public
	 * @param array $data - Campos de comentário a serem salvos.
	 * @return boolean - True ou False se o comentário for inserido com sucesso.  
	 */
	 //TODO: Executar SQL Diretamente, e verificar a necessidade da variável noFlags em DrumonModel
	public function saveComment($data) {
		$name = get_class($this->model);
		if(!empty($this->model->name)) $name = $this->model->name;
		$recordType = "Modules::".$name;
		$temp_table = $this->model->table;
		$this->model->table = "core_comments";

		$params = array();
		$params['name'] = $data['name'];
		$params['email'] = $data['email'];
		$params['site'] = $data['site'];
		$params['content'] = $data['content'];
		$params['record_id'] = $data['record_id'];
		$params['record_type'] = $recordType;
		$params['created_at'] = "now()";
		
		$result = $this->model->save($params);
		$this->model->table = $temp_table;
		return $result;
	}
}
?>
