<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */


	class CustomFields extends ModelBehavior {
		
		
		public function addCustomFields($records, &$params) {
			
			if(is_array(&$params['custom_fields'])){
				
				// Obtem o nome do record_type ex. Modules::BlogPost
				$name = get_class($this->model);
				if(!empty($this->model->name)) $name = $this->model->name;
				$record_type = "Modules::".$name;
				
				
				// Prepara os campos personalizados para a query. => 'video','fonte'
				$custom_fields = &$params['custom_fields'];
				$fields = array();
				foreach ($custom_fields as $field) {
					$fields[] = "'".$field."'";
				}
				$fields = implode($fields,',');
				
				
				// Prepara os ids dos registros para a query. => '1','2'
				$ids = array();
				foreach ($records as $record) {
					$ids[] = "'".$record['id']."'";
				}
				$ids = join(',',$ids);
				
				
				$query_metadata = "SELECT * FROM core_records_metadata WHERE `record_type` = '".$record_type."' AND `key` IN (".$fields.") AND `record_id` IN (".$ids.")";
				
				$metadatas = $this->model->connection->find($query_metadata);
				
				
				// Coloca todos os dados como vazio
				foreach ($records as $key => $value) {
					foreach ($custom_fields as $field) {
						$records[$key][$field] = '';
					}
				}
				
				
				foreach ($metadatas as $metadata) {
					$records[$metadata['record_id']][$metadata['key']] = $metadata['value'];
				}
				
			}
			
			
			return $records;
		}
		
		
	}
?>
