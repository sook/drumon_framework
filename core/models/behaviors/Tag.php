<?php
/**
 * Classe para tags dos models.
 *
 * @author Sook contato@sook.com.br
 * @package models
 * @subpackage behaviors
 */
class Tag extends AppBehavior {

	/**
	 * Retorna uma query com o id e o nome das tags do modelo.
	 *
	 * @access public
	 * @param int $id
	 * @return array
	 */
	public function getTags($id) {
		$recordType = "Modules::".get_class($this->model);
		$sql = "SELECT t.id, t.name 
					FROM core_tags t 
						INNER JOIN core_module_records_tags mt 
							ON (mt.core_tag_id = t.id) 
					WHERE mt.record_id = ".$id." AND mt.record_type = '".$recordType."'";
		return $this->model->query($sql);
	}
	
	/**
	 * Realiza uma consulta retornando registros que contenham a tag específicada.
	 *
	 * @access public
	 * @param array $params
	 * @return array
	 */
	public function findAllWithTags(&$params) {
		$name = get_class($this->model);
		if(!empty($this->model->name)) $name = $this->model->name;
		$recordType = "Modules::".$name;
		
		$tags = explode(',',$params['tags']);
		
		foreach ($tags as $key => $value) {
			$tags[$key] = '"'.$value.'"';
		}
		
	
		
		$query_tags = 'SELECT * FROM core_module_records_tags WHERE record_type = \''.$recordType.'\' AND tag_name IN ('.implode(',',$tags).')';
		
		if(count($query_tags) == 0){
			return false;
		}
		
		$result_tags = $this->model->connection->find($query_tags);
		$record_ids =  array();
		
		foreach ($result_tags as $row) {
			$record_ids[] = "'".$row['record_id']."'";
		}
		$record_ids = array_unique($record_ids);
		$params['where'] = "id in (".join(',',$record_ids).")";
		
		return $result_tags;
	}
}
?>
