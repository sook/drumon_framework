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
		$name = get_class($this->model);
		if(!empty($this->model->name)) $name = $this->model->name;
		$recordType = "Modules::".$name;
		$sql = 'SELECT * FROM core_comments  WHERE record_type = "'.$recordType.'" AND record_id = '.$id.' AND approved = 1 ORDER BY id ASC';
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
		$name = get_class($this->model);
		if(!empty($this->model->name)) $name = $this->model->name;
		$recordType = "Modules::".$name;
		$sql = 'SELECT COUNT(*) as total FROM core_comments WHERE record_type = "'.$recordType.'" AND record_id = '.$id.' AND approved = 1';
		$result = $this->model->query($sql);
		return $result[0]['total'];
	}

	/**
	 * Salva Comentários.
	 *
	 * @access public
	 * @param array $data - Campos de comentário a serem salvos.
	 * @param boolean $approved - Se true aprova o comentário na hora do cadastro. (default: false)
	 * @param boolean $cache_comment - Se true ele atualiza o campo de cache number_of_comments do registro. (default: false)
	 * @return boolean - True ou False se o comentário for inserido com sucesso.  
	 */
	 //TODO: Executar SQL Diretamente, e verificar a necessidade da variável noFlags em DrumonModel
	public function saveComment($data, $approved = false ,$cache_comment = false) {
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
		$params['approved'] = $approved ? 1 : 0;
		$params['record_type'] = $recordType;
		$params['created_at'] = "now()";
		
		$result = $this->model->save($params);
		$this->model->table = $temp_table;
		
		// Se não salvar retorna false;
		if(!$result) return $result;
		
		if($cache_comment){
			$sql = 'SELECT record_id, count(`record_id`) as total FROM `core_comments` WHERE record_type = "'.$recordType.'" AND record_id ="'.$data['record_id'].'" AND approved = 1 GROUP BY `record_id` order by total DESC';
			$result = $this->model->query($sql);
			
			$update_sql = 'UPDATE '.$this->model->table.' SET number_of_comments='.$result[0]['total'].' WHERE id='.$data['record_id'];
			$this->model->execute($update_sql);
		
		}
		return $result;
	}
	

}
?>
