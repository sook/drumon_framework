<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe para selects dos models.
 *
 * @author Sook contato@sook.com.br
 * @package models
 * @subpackage behaviors
 */
class Selector extends ModelBehavior {
	
	/**
	 * Busca todos os registros para o model.
	 *
	 * @access public
	 * @param array $params - Parâmetros para select na cláusula WHERE.
	 * @return array - Lista de dados retornados da query.
	 */
	public function find_allModelsUsingSelector(&$params) {
		// Obtem o tipo do registro.(polymorphic)
		$name = get_class($this->model);
		if(!empty($this->model->name)) $name = $this->model->name;
		$recordType = "Modules::".$name;
		$options = explode('&',$params['selector']);
		
  	$query_selector = 'SELECT record_id, count(record_id) as total FROM core_select_options_records WHERE record_type = \''.$recordType.'\'';
		$query_selector .= " AND (";
		
		foreach ($options as $option) {
			$values = explode('=',$option);
			$query_selector .= '(select_type_alias = "'.$values[0].'" AND select_option_alias = "'.$values[1].'") OR ';
		}
		$query_selector = rtrim($query_selector,"OR ");
		$query_selector .= ") GROUP BY record_id";
		
		
		$result_selector = $this->model->connection->find($query_selector);
		$total_options = count($options);
		$record_ids =  array();
		
		foreach ($result_selector as $result) {
			if($result['total'] == $total_options){
				$record_ids[] = "'".$result['record_id']."'";
			}
		}
		
		if(count($record_ids) == 0){
			return false;
		}
		
		$this->model->where_list[] = "id in (".join(',',$record_ids).")";
		return true;
	}
	
	
	//TODO - Checar caso de uso.
	/**
	 * 
	 *
	 * @access private
	 * @param array $label
	 * @return string
	 */
	private function getWhereLabel($label) {
		$values = explode("=",$label);
		return " (op.`core_select_alias` = '".$values[0]."' AND op.`alias` = '".$values[1]."') ";
	}

}
?>
