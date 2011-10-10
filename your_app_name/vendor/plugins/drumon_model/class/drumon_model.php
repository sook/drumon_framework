<?php
	
	/**
	 * Base model class
	 *
	 * @package models
	 */
	class DrumonModel {
		
		/**
		 * Table primary key name
		 *
		 * @var string
		 */
		public $primary_key = 'id';
		
		/**
		 * Table name to query database
		 *
		 * @var string
		 */
		public $table_name;
		
		/**
		 * List of all behaviors used by model
		 *
		 * @var array
		 */
		public $behaviors = array();
		
		/**
		 * Imported methods from behaviour
		 *
		 * @var string
		 */
		private $behaviors_methods = array();
		
		/**
		 * Number of records per page on pagination
		 *
		 * @var int
		 */
		public $per_page = 30;
		
		/**
		 * Options used to query a model
		 *
		 * @var array
		 */
		private $__query = array(
			'action' => 'select',
			'select' => '*'
		);
		
		/**
		 * List of statements used in query
		 *
		 * @var array
		 */
		private $__statements = array();
		
		/**
		 * PDO connection instance
		 *
		 * @var PDO
		 */
		protected $__connection;
		
		/**
		 * All model data
		 *
		 * @var string
		 */
		private $__data = array();
		
		public $__return_empty = false;
		
		/**
		 * Accesibles vars on mass assignment.
		 *
		 * @var array
		 */
		protected $attr_accessible = array();
		
		/**
		 * Protected vars on mass assignment.
		 *
		 * @var array
		 */
		protected $attr_protected = array();
		
		/**
		 * Column name list from table.
		 *
		 * @var array
		 */
		protected $column_names = array();
		
		/**
		 * List of methods fired before a record is created.
		 *
		 * @var array
		 */
		protected $before_create = array();
		
		/**
		 * List of methods fired before a record is saved. (create or update)
		 *
		 * @var array
		 */
		protected $before_save = array();
		
		/**
		 * List od methods fired before a record is updated.
		 *
		 * @var array
		 */
		protected $before_update = array();
		
		/**
		 * List of methods fired before delete a record.
		 *
		 * @var array
		 */
		protected $before_delete = array();
		
		/**
		 * List of methods fired after a record is created.
		 *
		 * @var array
		 */
		protected $after_create = array();
		
		/**
		 * List of methods fired after a record is saved. (create or update)
		 *
		 * @var string
		 */
		protected $after_save = array();
		
		/**
		 * List of methods fired after a record is updated.
		 *
		 * @var array
		 */
		protected $after_update = array();
		
		/**
		 * List of methods fired after delete a record.
		 *
		 * @var array
		 */
		protected $after_delete = array();
		
		/**
		 * Always after initialize a object this method is fired.
		 *
		 * @return void
		 */
		protected function after_initialize() {}
		
		
		/**
		 * Connect to database and load behaviors
		 *
		 * @param array $data 
		 */
		public function __construct($data = array()) {
			$this->__data = array_merge($this->__data, $data);
			$this->__connection = Connection::get_instance()->pdo;
			$this->add_behaviors($this->behaviors);
			$this->default_scope();
			$this->after_initialize();
		}
		
		/**
		 * Add Behaviors to model
		 *
		 * @param string $behaviors 
		 * @return void
		 */
		public function add_behaviors($behaviors) {
			foreach ($behaviors as $behavior) {
				require_once ORM_PATH.'/behaviors/'.$this->to_underscore($behavior).'.php';
				//Instância o objeto correspondente a classe passada.
				$new_import = new $behavior();
				//Obtém os métodos da classe
				$import_functions = get_class_methods($new_import);
				//Adiciona os métodos da classe informada
				foreach($import_functions as $function_name) {
					$this->behaviors_methods[$function_name] = &$new_import;
				}
			}
		}
		
		/**
		 * Try call undefined methods
		 *
		 * @param string $method 
		 * @param string $args 
		 * @return void
		 */
		public function __call($method, $args) {
			// Verifica se realmente existe o método desejado
			if(array_key_exists($method, $this->behaviors_methods)) {
				return call_user_func_array(array($this->behaviors_methods[$method], $method), array_merge(array(&$this),$args));
			}
			throw new Exception ("Call to undefined method: $method");
		}
		
		/**
		 * Change model data setters and add custom setters
		 *
		 * @param string $name 
		 * @param string $value 
		 * @return void
		 */
		public function __set($name, $value) {
			if (method_exists($this, 'set_'.$name)) {
				$method_name = 'set_'.$name;
				$this->$method_name($value);
				return;
			}
			$this->__data[$name] = $value;
		}
		
		/**
		 * Change model getters and add custom getters
		 *
		 * @param string $name 
		 * @return void
		 */
		public function __get($name) {
			
			if (method_exists($this, 'get_'.$name)) {
				$method_name = 'get_'.$name;
				return $this->$method_name();
			}
			
			if (isset($this->__data[$name])) {
				return $this->__data[$name];
			}
			// if (isset($this->__data[$name])) {
			// 	if (method_exists($this, 'get_'.$name)) {
			// 		$method_name = 'get_'.$name;
			// 		return $this->$method_name();
			// 	}
			// 	return $this->__data[$name];
			// }
			
			$trace = debug_backtrace();
			
			trigger_error(
				'Undefined property: ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE
			);
			//return null;
		}
		
		/**
		 * Create isset for undefined variables
		 *
		 * @param string $name 
		 * @return bool
		 */
		public function __isset($name) {
	  	return isset($this->__data[$name]);
	  }
		
		/**
		 * Create unset for undefined variables
		 *
		 * @param string $name 
		 * @return void
		 */
		public function __unset($name) {
	  	unset($this->__data[$name]);
	  }
		
		/**
		 * Read model attributes
		 *
		 * @param string $name 
		 * @return mixed
		 */
		public function read_attribute($name) {
			return $this->__data[$name];
		}
		
		/**
		 * Write a value on a model attribute
		 *
		 * @param string $name 
		 * @param string $value 
		 * @return void
		 */
		public function write_attribute($name, $value) {
			$this->__data[$name] = $value;
		}
		
		/**
		 * Get current sql query
		 *
		 * @param string $key 
		 * @return array|string
		 */
		public function get_query($key = false) {
			if ($key) {
				return $this->__query[$key];
			} else {
				return array('query' => $this->__query, 'statements' => $this->__statements);
			}
		}
		
		/**
		 * Set current sql query
		 *
		 * @param string $query 
		 * @return void
		 */
		public function set_query($values) {
			$this->__query = $values['query'];
			$this->__statements = $values['statements'];
		}
		
		/**
		 * Verify if record is new (is not on database)
		 *
		 * @return bool
		 */
		public function is_new() {
			return !(isset($this->__data[$this->primary_key]));
		}
		
		/**
		 * Save or update a record
		 *
		 * @param array $data 
		 * @param array $only_columns 
		 * @return bool
		 */
		public function save($data = array(), $only_columns = array()) {
			// TODO: melhorar esse update para ir somente os campos que foram alterados.
			return ($this->is_new()) ? $this->create($data, $only_columns) : $this->update($this->__data[$this->primary_key], array_merge($this->__data, $data));
		}
		
		/**
		 * Create a new record
		 *
		 * @param array $data 
		 * @param array $only_columns 
		 * @return bool
		 */
		public function create($data = array(), $only_columns = array()) {

			// Remove os campos a não serem salvos.
			$only_columns = ($only_columns) ? $only_columns : $this->attr_accessible;
			if ($only_columns) {
				foreach ($data as $key => $value) {
					if (!in_array($key,$only_columns)) {
						unset($data[$key]);
					}
				}
			} else {
				foreach ($this->attr_protected as $column) {
					unset($data[$column]);
				}
			}
			
			$this->__data = array_merge($this->__data, $data);
			
			// Executa os métodos de before_create
			if ($this->fire_hooks('before_create') === false) { return false; }
			if ($this->fire_hooks('before_save') === false) { return false; }

			$values = array(); 
			$columns = array();
			
			foreach ($this->__data as $key => $val) {
				$columns[] = '`'.$key.'`';
				$values[]  = ':'.$key.'';
			}

			$sql = 'INSERT INTO `'.$this->table_name.'` ('.implode(',',$columns).') VALUES ('.implode(',',$values).')';
			
			$saved = $this->__connection->prepare($sql)->execute($this->__data);
			
			if ($saved) {
				$this->id = $this->__connection->lastInsertId();
				$this->fire_hooks('after_create');
				$this->fire_hooks('after_save');
			}
			return $saved;
		}
		
		/**
		 * Update a record
		 *
		 * @param int $id 
		 * @param array $data 
		 * @param array $only_columns 
		 * @return bool
		 */
		public function update($id, $data = array(), $only_columns = array()) {
			// Remove the fields that will not be saved.
			$only_columns = ($only_columns) ? $only_columns : $this->attr_accessible;
			if ($only_columns) {
				foreach ($data as $key => $value) {
					if (!in_array($key,$only_columns)) {
						unset($data[$key]);
					}
				}
			} else {
				foreach ($this->attr_protected as $column) {
					unset($data[$column]);
				}
			}
			
			$this->__data = array_merge($this->__data, $data);
			
			// Fire all methods for before_update
			if ($this->fire_hooks('before_update') === false) { return false; }
			if ($this->fire_hooks('before_save') === false) { return false; }
			
			$values = array();
			foreach ($data as $key => $value) {
				$values[] = '`'.$key.'` = :'.$key.'';
			}
			
			$query = 'UPDATE `'.$this->table_name.'` SET '.implode(', ',$values).' WHERE `'.$this->primary_key.'` = '.$id;
			
			$result = $this->__connection->prepare($query)->execute($data);
			
			// If saved then fire all hooks.
			if ($result) {
				$this->fire_hooks('after_update');
				$this->fire_hooks('after_save');
			}
				
			return $result;
		}
		
		/**
		 * Delete a record
		 *
		 * @param int|array $ids
		 * @param bool $fire_callbacks 
		 * @return int Number of records deleted
		 */
		public function delete($ids = null, $fire_callbacks = true) {
			
			if (is_null($ids) && isset($this->id)) {				
				//$result = $this->exec('DELETE FROM `'.$this->table_name.'` WHERE `'.$this->primary_key.'` = '.$this->id);
				// TODO: desse jeito ele chama 2 vezes o modelo, uma pelo usuário outra pelo drumonmodel
				$result = $this->where(array($this->primary_key => $this->id))->delete_all($fire_callbacks);
			} else {
				$result = $this->where(array($this->primary_key => $ids))->delete_all($fire_callbacks);
			}
			
			return $result;
		}
		
		/**
		 * Delete all record found in query
		 *
		 * @param bool $fire_callbacks  
		 * @return int Number of records deleted
		 */
		public function delete_all($fire_callbacks = true) {
			
			// Delete all records without fire callbacks.
			if (!$fire_callbacks) {
				$this->__query['action'] = 'delete';
				$result = $this->__connection->exec($this->generate_sql());
				$this->clear_query();
				return $result;
			}
			
			// Find all records that will be deleted.
			$query_tmp = $this->get_query();
			$records = $this->all(true);
			
			// Fire all before_delete callbacks.
			foreach ($records as $record) {
				$record->fire_hooks('before_delete');
			}
			$this->set_query($query_tmp);
			$this->__query['action'] = 'delete';
			$stmt = $this->__connection->prepare($this->generate_sql());
			$stmt->execute($this->__statements);
			$result = $stmt->rowCount();
			
			// Clear object to next action.
			$this->clear_query();
			
			// If deleted then fire all after_delete callbacks
			if ($result) {
				foreach ($records as $record) {
					$record->fire_hooks('after_delete');
				}
			}
			
			return $result;
		}
		
		/**
		 * Increment a value from one int field
		 *
		 * @param mixed $id 
		 * @param string $column 
		 * @param int $value 
		 * @return bool
		 */
		public function increment($id, $column, $value = 1) {
			$value = (int) $value;
			if (is_int($value)) {
				return $this->exec('UPDATE `'.$this->table_name.'` SET `'.$column.'` = `'.$column.'` + '.$value.' WHERE `'.$this->primary_key.'` = "'.$id.'"');
			}
			return false;
		}
		
		/**
		 * Decrement a value from one int field
		 *
		 * @param mixed $id 
		 * @param string $column 
		 * @param int $value 
		 * @return bool
		 */
		public function decrement($id, $column, $value = 1) {
			$value = (int) $value;
			if (is_int($value)) {
				return $this->exec('UPDATE `'.$this->table_name.'` SET `'.$column.'` = `'.$column.'` - '.$value.' WHERE `'.$this->primary_key.'` = "'.$id.'"');
			}
			return false;
		}
		
		/**
		 * Call methods attached on hooks.
		 *
		 * @param string $hook_name 
		 * @return void
		 */
		public function fire_hooks($hook_name) {
			foreach ($this->$hook_name as $hook) {
				call_user_func(array($this, $hook));
			}
		}
		
		/**
		 * Execute a SQL expression.
		 *
		 * @param string $sql 
		 * @return mixed
		 */
		public function exec($sql) {
			return $this->__connection->exec($sql);
		}
		
		/**
		 * Select fields on query
		 *
		 * @param string $fields 
		 * @return object
		 */
		public function select($fields) {
			$this->__query['select'] = $fields;
			return $this;
		}
		
		/**
		 * Add SQL conditions for query
		 *
		 * $user->where('deleted = 1')->all();
		 * $user->where('name = ?',array('Mark'))->all();
		 * $user->where(array('name' => 'Mark'))->all();
		 *
		 * @param string $value 
		 * @param array $params 
		 * @return object
		 */
		public function where($where, $params = array()) {
			// Se where é um array então faz query simples com = ou IN se for o valor for um array.
			if (is_array($where)) {
				
				$where_new = array();
				$params_new = array();
				foreach ($where as $key => $value) {
					// Se for array usa IN
					if (is_array($value)) {
						$where_new[] = $key.' IN ('.implode(',', array_fill(0, count($value), '?')).')';
						foreach ($value as $v) {
							$params_new[] = $v;
						}
					} else {
						// Se não for array usa =
						$where_new[] = $key.' = ?';
						$params_new[] = $value;
					}
				}
				$where = implode(' AND ', $where_new);
				$params = $params_new;
			}
			
			$this->__statements = array_merge($this->__statements, $params);
			
			if (isset($this->__query['conditions'])) {
				$this->__query['conditions'] .= ' AND ';
			} else {
				$this->__query['conditions'] = '';
			}
			
			$this->__query['conditions'] .= '('.$where.')';
			return $this;
		}
		
		/**
		 * Add SQL JOIN on query
		 *
		 * @param string|array $joins 
		 * @return object
		 */
		public function join($joins) {
			if (!isset($this->__query['conditions'])) {
				$this->__query['joins'] = '';
			}
			
			// TODO: add nested joins
			if (is_array($joins)) {
				$join = '';
				foreach ($joins as $joinner) {
					$join_class_name = $this->to_camelcase($joinner);
					$join_class = new $join_class_name();
					$join .= ' INNER JOIN `'.$join_class->table_name.'` ON `'.$join_class->table_name.'`.'.$join_class->primary_key.' = `'.$this->table_name.'`.'.$joinner.'_id';
				}
			}else{
				$join = $joins;
			}
			
			$this->__query['joins'] .= ' '.$join;
			return $this;
		}
		
		/**
		 * Add SQL GROUP BY on query
		 *
		 * @param string $group 
		 * @return object
		 */
		public function group($group) {
			$this->__query['group'] = $group;
			return $this;
		}
		
		/**
		 * Add SQL HAVING to query
		 *
		 * @param string $having 
		 * @return object
		 */
		public function having($having) {
			$this->__query['having'] = $having;
			return $this;
		}
		
		/**
		 * Add SQL ORDER BY to query
		 *
		 * @param string $order 
		 * @return object
		 */
		public function order($order) {
			$this->__query['order'] = $order;
			return $this;
		}
		
		/**
		 * Add SQL LIMIT to query
		 *
		 * $user->limit(10)->all();
		 *
		 * @param string $limit 
		 * @return object
		 */
		public function limit($limit) {
			$this->__query['limit'] = $limit;
			return $this;
		}
		
		/**
		 * Add SQL LIMIT offset to query
		 *
		 * $user->limit(10)->offset(2)->all();
		 *
		 * @param string $offset 
		 * @return void
		 */
		public function offset($offset) {
			$this->__query['offset'] = $offset;
			return $this;
		}
		
		/**
		 * Check if one o more records exists.
		 *
		 * @param int|array $ids 
		 * @return bool
		 */
		public function exists($ids = null) {
			$this->select($this->primary_key);
			$ids = (is_array($ids)) ? $ids : array($ids);
			$total = count($ids);
			
			$this->limit($total);
			$result = $this->find($ids);
			return count($result) === $total;
		}
		
		/**
		 * Default scope for your model
		 *
		 * @return void
		 */
		public function default_scope() {
			return $this;
		}
		
		/**
		 * Create a SQL from all query data
		 *
		 * @return string
		 */
		public function generate_sql() {
			
			//$this->default_scope();
			
			$action = 'SELECT '.$this->__query['select'];
			
			// Ação de deletar
			if ($this->__query['action'] === 'delete') {
				$action = 'DELETE';
			}
			
			$sql = array();
			$sql[] = $action.' FROM `'.$this->table_name.'`';
			
			if (isset($this->__query['joins'])) {
				$sql[] = $this->__query['joins'];
			}
			
			if (isset($this->__query['conditions'])) {
				$sql[] = ' WHERE '.$this->__query['conditions'];
			}
			
			if (isset($this->__query['group'])) {
				$sql[] = ' GROUP BY '.$this->__query['group'];
			}
			
			if (isset($this->__query['having'])) {
				$sql[] = ' HAVING '.$this->__query['having'];
			}
			
			if (isset($this->__query['order'])) {
				$sql[] = ' ORDER BY '.$this->__query['order'];
			}
			
			if (isset($this->__query['limit'])) {
				$sql[] = ' LIMIT '.$this->__query['limit'];
			}
			
			if (isset($this->__query['limit']) && isset($this->__query['offset'])) {
				$sql[] = ' OFFSET '.$this->__query['offset'];
			}
			
			return implode($sql);
		}
		
		/**
		 * Return number of the all query records
		 *
		 * @param string $column 
		 * @return int
		 */
		public function count($column = "*") {
			$this->select('count('.$column.') as total');
			$result = $this->all();
			return $result[0]['total'];
		}
		
		/**
		 * Return average from a column
		 *
		 * @param string $column 
		 * @return int
		 */
		public function average($column) {
			$this->select('AVG('.$column.') as average');
			$result = $this->all();
			return $result[0]['average'];
		}
		
		/**
		 * Return minimum value from column
		 *
		 * @param string $column 
		 * @return int
		 */
		public function minimum($column) {
			$this->select('MIN('.$column.') as minimum');
			$result = $this->all();
			return $result[0]['minimum'];
		}
		
		/**
		 * Return maximum value from column
		 *
		 * @param string $column 
		 * @return int
		 */
		public function maximum($column) {
			$this->select('MAX('.$column.') as maximum');
			$result = $this->all();
			return $result[0]['maximum'];
		}
		
		/**
		 * Return sun from column
		 *
		 * @param string $column 
		 * @return int
		 */
		public function sum($column) {
			$this->select('SUM('.$column.') as total');
			$result = $this->all();
			return $result[0]['total'];
		}
		
		/**
		 * Find records using one SQL query
		 *
		 * $user->find_by_sql('SELECT * FROM users');
		 *
		 * @param string $sql 
		 * @param array $statement 
		 * @param bool $object 
		 * @return mixed
		 */
		public function find_by_sql($sql, $statement = array(), $object = false) {
			$stmt = $this->__connection->prepare($sql);
			$stmt->execute($statement);

			if ($object) {
				$model_list = $stmt->fetchALL(PDO::FETCH_CLASS, get_class($this));
			} else {
				$model_list = $stmt->fetchALL(PDO::FETCH_ASSOC);
			}
			return $model_list;
		}
		
		/**
		 * Find record by id or list of ids
		 *
		 * @param int|array $ids 
		 * @param string $object 
		 * @return mixed
		 */
		public function find($ids, $object = false) {
			if (is_array($ids)) {
				$this->where($this->table_name.'.'.$this->primary_key.' IN ('.implode(',',$ids).')');
				return $this->all($object);
			} else {
				$this->where($this->table_name.'.'.$this->primary_key.' = '.$ids);
				$result = $this->all($object);
				return $result[0];
			}
		}
		
		// TODO:
		// dica usar array para ser mais rapido?
		public function find_each($number, $start = 1) {
			// conta todos os registros depois do start.
			// divide todos os registros pelo numero do batch.
			// faz um loop com essa divissão.
			  // pega os registros a partir do start
				// guarda cada resultado do number em uma array
			// retorna a array
		}
		
		/**
		 * Return first result on table
		 *
		 * @param string $object 
		 * @return mixed
		 */
		public function first($object = false) {
			return $this->limit(1)->order($this->table_name.'.'.$this->primary_key.' ASC')->one($object);
		}
		
		/**
		 * Return last result on table
		 *
		 * @param string $object 
		 * @return mixed
		 */
		public function last($object = false) {
			return $this->limit(1)->order($this->table_name.'.'.$this->primary_key.' DESC')->one($object);
		}
		
		/**
		 * Return one record
		 *
		 * @param string $object 
		 * @return mixed
		 */
		public function one($object = false) {
			$this->__query['limit'] = 1;
			$result = $this->all($object);
			if ($result) {
				return $result[0];
			}
			return false;
		}
		
		/**
		 * Return all records matcheds on query
		 *
		 * @param string $object 
		 * @return mixed
		 */
		public function all($object = false) {
			
			if ($this->__return_empty) {
				return array();
			}
			
			$sql = $this->generate_sql();

			$stmt = $this->__connection->prepare($sql);
			$stmt->execute($this->__statements);
			
			$this->clear_query();

			if ($object) {
				$model_list = $stmt->fetchALL(PDO::FETCH_CLASS, get_class($this));
			} else {
				$model_list = $stmt->fetchALL(PDO::FETCH_ASSOC);
			}
			return $model_list;
		}
		
		/**
		 * Clear all query data from object
		 *
		 * @return object
		 */
		public function clear_query() {
			// Limpa a query.
			$this->__query = array(
				'action' => 'select',
				'select' => '*'
			);
			// Limpa os dados da query.
			$this->__statements = array();
			
			$this->default_scope();
		}
		
		/**
		 * Return SQL generate from all data query
		 *
		 * @return string
		 */
		public function to_sql() {
			return $this->interpolate_query($this->generate_sql(), $this->__statements);
		}
		
		/**
		 * Replaces any parameter placeholders in a query with the value of that
		 * parameter. Useful for debugging. Assumes anonymous parameters from 
		 * $params are are in the same order as specified in $query
		 *
		 * @param string $query The sql query with parameter placeholders
		 * @param array $params The array of substitution parameters
		 * @return string The interpolated query
		 *
		 * @author http://stackoverflow.com/questions/210564/pdo-prepared-statements/1376838#1376838
		 */
		public static function interpolate_query($query, $params) {
			$keys = array();

			# build a regular expression for each parameter
			foreach ($params as $key => $value) {
				if (is_string($key)) {
					$keys[] = '/:'.$key.'/';
				} else {
					$keys[] = '/[?]/';
				}
				$params[$key] = "'".$value."'";
			}
			$query = preg_replace($keys, $params, $query, 1, $count);
			return $query;
		}
		
		/**
		 * Get all colunm names from current table
		 *
		 * @return array
		 */
		public function get_column_names() { 

			#$sql = "SELECT column_name FROM information_schema.columns WHERE table_name = '".$this->table_name."' AND table_schema = '".Connection::get_instance()->config['database']."'";
			$sql = 'SHOW COLUMNS FROM ' . $this->table_name; 

			$stmt = $this->__connection->prepare($sql); 

			try {     
				if($stmt->execute()){ 
					$raw_column_data = $stmt->fetchAll(PDO::FETCH_ASSOC); 
					foreach($raw_column_data as $outer_key => $array){ 
						$this->column_names[$array['Field']] = $array; 
					} 
				} 
				return $this->column_names; 
			} catch (Exception $e){ 
				return $e->getMessage();
			}         
		}
		
		/**
		 * Return a string underscored
		 *
		 * @param string $camelCasedWord 
		 * @return string
		 */
		public function to_underscore($camelCasedWord) {
			$result = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
			$result = str_replace(' ', '_', $result);
			return $result;
		}
		
		
		/**
		 * Transforma palavras_em_underscore em PalavrasEmCamelCase
		 *
		 * @param string $lowerCaseAndUnderscoredWord 
		 * @return string
		 */
		public function to_camelcase($lowerCaseAndUnderscoredWord) {
			$lowerCaseAndUnderscoredWord = ucwords(str_replace('_', ' ', $lowerCaseAndUnderscoredWord));
			$result = str_replace(' ', '', $lowerCaseAndUnderscoredWord);
			return $result;
		}
	}
?>