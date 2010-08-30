<?php
/**
 * Classe de banco de dados do framework.
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class SKDatabase {
	
	/** 
	 * Conexão do banco de dados.
	 *
	 * @access private
	 * @var object
	 */
	private $connection = null;

	/** 
	 * Mantém a referência da classe SKDatabase.
	 *
	 * @access private
	 * @static
	 * @var object
	 */
	private static $SKDatabase = null;

	/**
	 * Efetua a conexão com o banco de dados.
	 *
	 * @access private
	 * @return void
	 */
	private function __construct(){
		$this->connect();
	}

	/**
	 * Instancia a classe caso a variável $SKDatabase for nula.
	 *
	 * @access public
	 * @return object - Instância de conexão da classe.
	 */
	public static function getInstance(){
		if (self::$SKDatabase === null){
			self::$SKDatabase = new SKDatabase();
		}
		return self::$SKDatabase;
	}

	/**
	 * Finaliza conexão.
	 *
	 * @access public
	 * @return void
	 */
	public function __destruct(){
		mysql_close($this->connection);
	}

	/**
	 * Realiza conexão com o bano de dados mysql.
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
	 * Disconecta do banco de dados mysql.
	 *
	 * @access public
	 * @return void
	 */
	public function disconnect(){
		mysql_close($this->connection);
		$this->connection = null;
	}

	/**
	 * Executa o comando sql informado.
	 *
	 * @access public
	 * @param string $sql - Query a ser executada.
	 * @return object - Retorna o resultado da consulta.
	 */
	public function query($sql){
		$result = mysql_query($sql, $this->connection);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		return $result;
	}

	/**
	 * Executa uma query (SELECT), aplicando o método <b>mysql_fetch_assoc()</b>.
	 *
	 * @access public
	 * @param string $sql - Consulta a ser executada.
	 * @return array - Lista de dados retornados da query.
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
	 * Coloca a chave do array o valor de um campo.
	 *
	 * @access public
	 * @param string $sql - Query a ser executada.
	 * @param string $key - Atributo chave a ser procurado na lista.
	 * @return array - Lista de dados retornados da query.
	 */
	 // TODO: Refatorar nome do método.
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
	 * Salva dados no banco.
	 *
	 * @access public
	 * @param string $table - Tabela onde serão salvos os dados.
	 * @param array $data - Dados a serem inseridos no banco.
	 * @return mixed - Int com o número do novo registro, aplicando o método <b>mysql_insert_id()</b> ou false caso nada tenha ocorrido
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
	 * Escapa os caracteres do mysql.
	 *
	 * @access public
	 * @param string $string - String Sql.
	 * @return string - String com escape.
	 * @ignored
	 */
	// TODO - Código duplicado.
	function escape($string) {
		if(get_magic_quotes_runtime()) $string = stripslashes($string);
		return mysql_real_escape_string($string);
	}

}?>
