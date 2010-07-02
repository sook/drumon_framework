<?php
/**
 * Classe de banco de dados do framework
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class SKDatabase {
	
	/** 
	 * Conexão do banco de dados
	 *
	 * @access private
	 * @name $connection
	 */
	private $connection = null;

	/** 
	 * Mantém a referência da classe SKDatabase
	 *
	 * @access private
	 * @static
	 * @name $SKDatabase
	 */
	private static $SKDatabase = null;

	/**
	 * Construtor
	 * @access private
	 * @return void
	 */
	private function __construct(){
		$this->connect();
	}

	/**
	 * Instancia a classe caso a variável $SKDatabase for nula
	 *
	 * @access public
	 * @return object
	 */
	public static function getInstance(){
		if (self::$SKDatabase === null){
			self::$SKDatabase = new SKDatabase();
		}
		return self::$SKDatabase;
	}

	/**
	 * Finaliza conexão
	 *
	 * @access public
	 * @return object
	 */
	public function __destruct(){
		mysql_close($this->connection);
	}

	/**
	 * Conecta o banco de dados mysql
	 *
	 * @access public
	 * @return void
	 */
	public function connect(){
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die("Não foi possível conectar ao banco de dados, verifique os seus dados.");
		mysql_select_db(DB_NAME, $this->connection) or die("O banco de dados informado não existe.");
		$this->query("SET NAMES '".CHARSET."'"); //persian support
	}

	/**
	 * Disconecta do banco de dados mysql
	 *
	 * @access public
	 * @return void
	 */
	public function disconnect(){
		mysql_close($this->connection);
		$this->connection = null;
	}

	/**
	 * Executa o comando sql informado
	 *
	 * @access public
	 * @param string $sql query a ser executada
	 * @return mixed
	 */
	public function query($sql){
		$result = mysql_query($sql, $this->connection);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		return $result;
	}

	/**
	 * Executa uma query (SELECT), aplicando o método <b>mysql_fetch_assoc()</b>
	 *
	 * @access public
	 * @param string $sql query a ser executada
	 * @return array
	 */
	public function find($sql){
		$rows = array();
		$result = $this->query($sql);
		while($r = mysql_fetch_assoc($result)) {
			$rows[] = $r;
		}
		mysql_free_result($result);
		return $rows;
	}

	/**
	 * Coloca a chave do array o valor de um campo
	 *
	 * @access public
	 * @param string $sql query a ser executada
	 * @return mixed
	 */
	public function find_with_key($sql,$key){
		$rows = array();
		$result = $this->query($sql);
		while($r = mysql_fetch_assoc($result)) {
			$rows[$r[$key]] = $r;
		}
		mysql_free_result($result);
		return $rows;
	}

	/**
	 * Salva dados no banco de dados
	 *
	 * @access public
	 * @param string $table
	 * @param array $data
	 * @return mixed int com o número do novo registro, aplicando o método <b>mysql_insert_id()</b> ou false caso nada tenha ocorrido
	 */
	public function save($table, $data) {
		$query = "INSERT INTO `".$table."` ";
		$values = ''; $columns = '';

		foreach ($data as $key => $val) {
			$columns .= "`$key`, ";
			if(strtolower($val)=='null') $values .= "NULL, ";
			elseif(strtolower($val)=='now()') $values .= "NOW(), ";
			else $values .= "'".$this->escape($val)."', ";
		}

		$query .= "(". rtrim($columns, ', ') .") VALUES (". rtrim($values, ', ') .");";

		if ($this->query($query)) {
			return mysql_insert_id();
		}
		return false;
	}

	/**
	 * Escapa os caracteres do mysql
	 *
	 * @access public
	 * @param string $data
	 * @return string
	 */
	function escape($string) {
		if(get_magic_quotes_runtime()) $string = stripslashes($string);
		return mysql_real_escape_string($string);
	}

}?>
