<?php


	/**
	 * Database connection class with PDO using Singleton Partner 
	 *
	 * @package models
	 */
	class Connection {
		
		/**
		 * Connection instance
		 *
		 * @var object
		 */
		private static $instance;
		
		/**
		 * PDO Object
		 *
		 * @var object
		 */
		public $pdo;
		
		/**
		 * Configurations for connection
		 *
		 * @var array
		 */
		public $config = array('charset'=>'utf8');
		
		/**
		 * Protect singleton
		 *
		 */
		private function __construct() { }
		
		/**
		 * Connect to database using PDO
		 *
		 * @param array $config 
		 * @return void
		 */
		public function connect($config) {
			$this->config = array_merge($this->config, $config);
			$driver_options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->config['charset']);
			try {
				$this->pdo = new PDO("mysql:host=".$this->config['host'].";dbname=".$this->config['database']."", $this->config['user'], $this->config['password'],$driver_options);
				$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			} catch (PDOException $e) {
				echo $e->getMessage();
				die();
			}
		}
		
		/**
		 * Get a singleton instance of Connection<object>
		 *
		 * @return object
		 */
		public static function get_instance() {
			if (self::$instance === null) {
				self::$instance = new Connection();
			}
			
			return self::$instance;
		}
	}
?>