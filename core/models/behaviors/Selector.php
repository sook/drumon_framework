<?php
/**
 * Classe para selects dos models.
 *
 * @author Sook contato@sook.com.br
 * @package models
 * @subpackage behaviors
 */
class Selector extends AppBehavior {
	
	/**
	 * Busca todos os registros para o model.
	 *
	 * @access public
	 * @param array $params 
	 * @return array
	 */
	public function findAllModelsUsingSelector(&$params) {

		// Obtem o tipo do registro.(polymorphic)
		$name = get_class($this->model);
		if(!empty($this->model->name)) $name = $this->model->name;
		$recordType = "Modules::".$name;

  	$query_selector = 'SELECT * FROM core_select_options_records WHERE record_type = \''.$recordType.'\'';

		$p = explode('=',$params['select']);
		$query_selector .= " AND (";

		// categoria=esporte|politica
		$or_values = explode('\|',$p[1]);

		foreach ($or_values as $value) {
			$query_selector .= " (select_type_alias = '".$p[0]."' AND select_option_alias = '".$value."')";
			$query_selector .= " OR";
		}

		$query_selector = rtrim($query_selector,"OR ");
		$query_selector .= ")";


		$result_selector = $this->model->connection->find($query_selector);
		$record_ids =  array();

		foreach ($result_selector as $row) {
			$record_ids[] = "'".$row['record_id']."'";
		}

		$params['where'] = "id in (".join(',',$record_ids).")";

		// Usado para aproveitar no include.
		return $result_selector;
	}
	
	/**
	 * Busca todos os registros para o model.
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
