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
		 * Reset query after finish if true
		 *
		 * @var string
		 */
		private $__reset = true;
		
		/**
		 * PDO connection instance
		 *
		 * @var object
		 */
		protected $__connection;
		
		/**
		 * All model data
		 *
		 * @var string
		 */
		private $__data = array();
		
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
		 * Connect to database and load behaviors
		 *
		 * @param array $data 
		 */
		public function __construct($data = array()) {
			//$this->__data = $data; // removi pq ele resetava na hora de criar os objetos via pdo
			$this->__connection = Connection::get_instance()->pdo;
			$this->add_behaviors($this->behaviors);
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
		 * @author Danillo César de Oliveira Melo
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
		 * @author Danillo César de Oliveira Melo
		 */
		public function __get($name) {
			if (isset($this->__data[$name])) {
				if (method_exists($this, 'get_'.$name)) {
					$method_name = 'get_'.$name;
					return $this->$method_name();
				}
				return $this->__data[$name];
			}
			
			$trace = debug_backtrace();
			trigger_error(
				'Undefined property via __get(): ' . $name .
				' in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE
			);
			return null;
		}
		
		/**
		 * Create isset for undefined variables
		 *
		 * @param string $name 
		 * @return boolean
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
		 * Save or update a record
		 *
		 * @param array $data 
		 * @param array $only_columns 
		 * @return boolean
		 */
		public function save($data = array(), $only_columns = array()) {
			// TODO: melhorar esse update para ir somente os campos que foram alterados.
			return (isset($this->__data[$this->primary_key])) ? $this->update($this->__data[$this->primary_key], array_merge($this->__data, $data)) : $this->create($data, $only_columns);
		}
		
		/**
		 * Create a new record
		 *
		 * @param array $data 
		 * @param array $only_columns 
		 * @return boolean
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
			$values = array(); 
			$columns = array();
			
			foreach ($this->__data as $key => $val) {
				$columns[] = '`'.$key.'`';
				$values[]  = ':'.$key.'';
			}

			$query = 'INSERT INTO `'.$this->table_name.'` ('.implode(',',$columns).') VALUES ('.implode(',',$values).')';
			$saved = $this->__connection->prepare($query)->execute($this->__data);
			
			if ($saved) {
				$this->id = $this->__connection->lastInsertId();
			}
			return $saved;
		}
		
		/**
		 * Update a record
		 *
		 * @param int $id 
		 * @param array $data 
		 * @param array $only_columns 
		 * @return boolean
		 */
		public function update($id, $data = array(), $only_columns = array()) {
			
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
			
			$values = array();
			foreach ($data as $key => $value) {
				$values[] = '`'.$key.'` = :'.$key.'';
			}
			
			$query = 'UPDATE `'.$this->table_name.'` SET '.implode(', ',$values).' WHERE `'.$this->primary_key.'` = '.$id;
			return $this->__connection->prepare($query)->execute($data);
		}
		
		/**
		 * Delete a record
		 *
		 * @param int|array $ids 
		 * @return int Number of records deleted
		 */
		public function delete($ids = null) {
			if (is_null($ids) && isset($this->id)) {
				$result = $this->__connection->exec('DELETE FROM `'.$this->table_name.'` WHERE `'.$this->primary_key.'` = '.$this->id);
			} else {
				if(is_array($ids)) {
					$result = $this->__connection->exec('DELETE FROM `'.$this->table_name.'` WHERE `'.$this->primary_key.'` IN ('.implode(',',$ids).')');
				} else {
					$result = $this->__connection->exec('DELETE FROM `'.$this->table_name.'` WHERE `'.$this->primary_key.'` = '.$ids);
				}
			}
			return $result;
		}
		
		/**
		 * Delete all record found in query
		 *
		 * @return int Number of records deleted
		 */
		public function delete_all() {
			$this->__query['action'] = 'delete';
			return $this->__connection->exec($this->generate_sql());
		}
		
		/**
		 * Increment a value from one int field
		 *
		 * @param mixed $id 
		 * @param string $column 
		 * @param int $value 
		 * @return boolean
		 */
		public function increment($id, $column, $value = 1) {
			if (is_int($value)) {
				return $this->__connection->exec('UPDATE `'.$this->table_name.'` SET `'.$column.'` = `'.$column.'` + '.$value.' WHERE `'.$this->primary_key.'` = "'.$id.'"');
			}
			return false;
		}
		
		/**
		 * Decrement a value from one int field
		 *
		 * @param mixed $id 
		 * @param string $column 
		 * @param int $value 
		 * @return boolean
		 */
		public function decrement($id, $column, $value = 1) {
			if (is_int($value)) {
				return $this->__connection->exec('UPDATE `'.$this->table_name.'` SET `'.$column.'` = `'.$column.'` - '.$value.' WHERE `'.$this->primary_key.'` = "'.$id.'"');
			}
			return false;
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
		 * @return boolean
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
		 * Create a SQL from all query data
		 *
		 * @return string
		 */
		public function generate_sql() {
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
		 * Clear all query data from object
		 *
		 * @return object
		 */
		public function clear_query() {
			$this->__query = array(
				'action' => 'select',
				'select' => '*'
			);
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
		 * @param boolean $object 
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
			$sql = $this->generate_sql();

			// Limpa a query
			if ($this->__reset) {
					$this->clear_query();
					$this->__reset = true;
			}
			
			$stmt = $this->__connection->prepare($sql);
			$stmt->execute($this->__statements);

			if ($object) {
				$model_list = $stmt->fetchALL(PDO::FETCH_CLASS, get_class($this));
			} else {
				$model_list = $stmt->fetchALL(PDO::FETCH_ASSOC);
			}
			return $model_list;
		}
		
		/**
		 * No reset current query after execute
		 *
		 * @return object
		 */
		public function no_reset() {
			$this->__reset = false;
			return $this;
		}
		
		/**
		 * Return SQL generate from all data query
		 *
		 * @return string
		 */
		public function to_sql() {
			return $this->interpolateQuery($this->generate_sql(), $this->__statements);
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
		public static function interpolateQuery($query, $params) {
			$keys = array();

			# build a regular expression for each parameter
			foreach ($params as $key => $value) {
				if (is_string($key)) {
					$keys[] = '/:'.$key.'/';
				} else {
					$keys[] = '/[?]/';
				}
			}
			$query = preg_replace($keys, $params, $query, 1, $count);
			return $query;
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