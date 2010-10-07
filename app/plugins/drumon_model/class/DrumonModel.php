<?php

/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

require(ROOT."/plugins/drumon_model/class/Database.php");
require(ROOT.'/plugins/drumon_model/class/ModelBehavior.php');
require(ROOT.'/plugins/drumon_model/AppModel.php');



/**
 * Classe abstrata que fornece suporte a classe base de modelo, para integração com o Sook CMS.
 *
 * @package class
 * @abstract 
 * @author Sook contato@sook.com.br
 */
abstract class DrumonModel {
// TODO Comentar $noFlagTables
	
	/**
	 *  Define como não publicado qualquer registro do banco.
	 */
	const NO_PUBLISHED = 0;
	
	/**
	 *  Define como publicado qualquer registro do banco.
	 */
	const PUBLISHED = 1;
	
	/**
	 *  Define a situação que o registro estar na lixeira.
	 */
	const DRAFT = 2;

	/** 
	 * Seta os parâmetros padrões para serem usados nas instruções sql.
	 *
	 * @access public
	 * @var array
	 */
	public $params = array('fields' => '*', 'where' => 1, 'join' => '', 'order' => '`order` DESC','group_by'=>'','include'=>array());

	/** 
	 * Lista com as funções importadas do Behavior.
	 *
	 * @access private
	 * @var array
	 */
	private $imported_functions = array();
	
	/** 
	 * Mantém a referência da classe SKDatabase.
	 *
	 * @access public
	 * @var object
	 */
	public $connection;

	/** 
	 * Quantidade de registro por página.
	 *
	 * @access public
	 * @var integer
	 */
	public $per_page = 10;

	/** 
	 * Lista de comportamentos utilizados no módulo. Opções: array('trash','status')
	 *
	 *
	 * @access protected
	 * @var array
	 */
	protected $uses = array();
	
	/** 
	 * Lista os parâmetros utilizados pela cláusula WHERE.
	 *
	 * @access private
	 * @var array
	 */
	private $usesColumns = array('trash' => '`deleted` = 0', 'status' => '`status` = 1');
	
	/** 
	 * @ignore
	 *
	 * @access private
	 * @var array
	 */
	private $noFlagTables = array('core_comments');

	/** 
	 * Cache da consulta realizada pelo parâmetro selector evitando nova consulta no include do find.
	 *
	 * @access protected
	 * @var array
	 */
	protected $cache = array();
	
	/** 
	 * Nome da tabela do modelo.
	 *
	 * @access protected
	 * @var string
	 */
	protected $table = "";
	
	/** 
	 * Nome da chave primaria do modelo.
	 *
	 * @access protected
	 * @var string
	 */
	protected $primaryKey = "id";
	
	
	
	public $where_list = array();


	/**
	 * Obtém a conexão com o banco de dados e define o nome da tabela automático caso a 
	 * variável $table não esteja definida.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->connection = Database::getInstance();
		if(empty($this->table)) {
			$this->table = strtolower(get_class($this));
		}
		$this->imports('CustomFields');
	}

	/**
	 * Chama métodos behaviors
	 *
	 * @access public
	 * @param array $method  
	 * @param array $args
	 * @return mixed
	 * @ignore
	 */
	public function __call($method, $args) {
		// Verifica se realmente existe o método desejado
		if(array_key_exists($method, $this->imported_functions)) {
			//			$args[] = $this;
			return call_user_func_array(array($this->imported_functions[$method], $method), $args);
		}
		throw new Exception ('Verifique se você chamou o método import no modelo: ' . $method);
	}

	/**
	 * Importa as funções existentes da classe passada por parâmetro, que pertence a um behaviors. 
	 * Simula herança múltipla.
	 *
	 * @access protected
	 * @param string $class - Nome da classe a ser importada.
	 * @return void
	 */
	protected function imports($class) {
		// TODO adicionel na linha abaixo tava $ coloquei $class 
		require_once ROOT."/plugins/drumon_model/behaviors/".$class.".php";

		//Instância o objeto correspondente a classe passada.
		$new_import = new $class(&$this);

		//Obtém os métodos da classe
		$import_functions = get_class_methods($new_import);

		//Adiciona os métodos da classe informada
		foreach($import_functions as $function_name) {
			$this->imported_functions[$function_name] = &$new_import;
		}
	}

	/**
	 * Executa uma query (SELECT), aplicando o método <b>mysql_fetch_assoc()</b>.
	 *
	 * @access public
	 * @param string $sql - Query a ser executada.
	 * @return array - Lista de dados retornados da query.
	 */
	public function query($sql) {
		return $this->connection->find($sql);
	}

	/**
	 * Executa o comando sql passado.
	 * 
	 * @access public
	 * @param string $sql Query a ser executada.
	 * @return mixed
	 */
	public function execute($sql) {
		return $this->connection->query($sql);
	}

	/**
	 * Busca todos os registros.
	 * 
	 * Exemplo:
	 *
	 * <code>
	 * $post = new Post();
	 * $posts = $post->findAll(array(
	 * 	'fields' => 'id,title',
	 * 	'include' => array('tags','comments_number','photos','selector'),
	 *  'custom_fields' => array('video','user'),
	 * 	'selector' => 'category=car',
	 * 	'tags' => 'php,html',
	 * 	'where' => 'title = "danillo"',
	 * 	'limit' => 10,
	 * 	'order' => 'id DESC'
	 * ));
	 * </code>

	 * @access public
	 * @param array $params - (Opcional) <br/>
	 * fields => Nomes das colunas separados por vírgula que retornarão no resultado da consulta sql. Se vazio retorna todos os campos. <br/>
	 * include => Funções seletoras auxiliares incluídas para fazer consultas padronizadas e distintas por um atributo.<br/>
	 * selector => Nome das colunas que servirão para distinguir os dados através de categrias.<br/>
	 * tags => Tags (strings) para filtrar as consulas por determinados atributos.<br/>
	 * where => Usada para extrair apenas os registros que satisfazem o critério especificado.<br/>
	 * limit => Usada para extrair os registros limitando a uma quantidade de resultados.<br/>
	 * order => Usada para ordernar os registros.<br/>
	 * @return mixed array com os valores do(s) elemento(s) consultado(s) ou false caso não encontre nenhum elemento.
	 */
	public function findAll($params = array()) {
		$params = array_merge($this->params, $params);
		
		// Reseta a lista de where list
		$this->where_list = array();
		$this->where_list[] = $params['where'];
		
		
		
		if(!$this->addBehaviorsContent(&$params)){
			return false;
		}
		
		
		$wheres = '('.join(' AND ',$this->where_list).')';

		
		$sql = "SELECT ".$params['fields']." FROM ".$this->table;
		$sql .= " ".$params['join']." ";
		$sql .=	" WHERE ".$this->addCoreWheres($wheres);
		$sql .= " ".$params['group_by'];
		$sql .= " ORDER BY ".$params['order'];
		$sql .= (!empty($params['limit'])? " LIMIT ".$params['limit']:"");
		
		
		$records = $this->connection->find_with_key($sql,$this->primaryKey);
		
		$record_size = count($records);
		if($record_size === 0){
			return false;
		}
		
		
		
		$records = $this->addCustomFields($records, &$params);

		//Se não tiver algum include já retorna.
		if(count($params['include']) === 0){
			return $records;
		}

		//Adiciona novos atributos do cms ao registro.
		$ids = array();
		foreach ($records as $record) {
			$ids[] = "'".$record['id']."'";
		}

		$ids = join(',',$ids);

		//Inclui nos registros seus selects e options.
		if(in_array('selectors',$params['include'])) {
			$name = get_class($this);
			if(!empty($this->name)) $name = $this->name;
			$recordType = "Modules::".$name;

			$selects = $this->connection->find('SELECT * FROM core_select_options_records WHERE record_type = \''.$recordType.'\' AND record_id IN ('.$ids.')');

			foreach ($records as $key => $value) {
				$records[$key]['selectors'] = array();
			}
			
			foreach ($selects as $select) {
				if(is_array($records[$select['record_id']]['selectors'])){
					$records[$select['record_id']]['selectors'][$select['select_type_alias']] = array('name'=>$select['select_option_name'],'alias'=>$select['select_option_alias']);
				}else{
					$records[$select['record_id']]['selectors'] = array($select['select_type_alias'] => array('name'=>$select['select_option_name'],'alias'=>$select['select_option_alias']));
				}
			}
		}
		
		//Adiciona as tags ao resultado
		if(in_array('tags',$params['include'])){
			// Busca as tags de cada registro.
			$name = get_class($this);
			if(!empty($this->name)) $name = $this->name;
			$recordType = "Modules::".$name;
			$tags = $this->connection->find('SELECT * FROM core_module_records_tags WHERE record_type = \''.$recordType.'\' AND record_id IN ('.$ids.')');

			// Seta as tags nos registros como array vazio
			foreach ($records as $key => $value) {
				$records[$key]['tags'] = array();
			}

			// Junta as tags ao registro.
			foreach ($tags as $tag) {
				if(is_array($records[$tag['record_id']]['tags'])) {
					$records[$tag['record_id']]['tags'][$tag['core_tag_id']] = $tag['tag_name'];
				}else {
					$records[$tag['record_id']]['tags'] = array($tag['core_tag_id'] => $tag['tag_name']);
				}
			}
		}

		//TODO: Usar array_filter
		if(in_array('photos',$params['include'])){
			$ids = array();
			foreach ($records as $record) {
				$ids[] = "'".$record['gallery_id']."'";
			}
			$ids = join(',',$ids);

			$recordType = "Modules::".$this->getModelName();
			$photos = $this->connection->find('SELECT * FROM core_images WHERE gallery_id IN ('.$ids.')');
			$gallery_ids = array();
			foreach ($photos as $photo) {
				$gallery_ids[] = $photo['gallery_id'];
			}
			$gallery_ids = array_unique($gallery_ids);

			// Seta as photos nos registros como array vazio
			foreach ($records as $key => $value) {
				$photos = $this->filterByValue($photos,'gallery_id',$records[$key]['gallery_id']);

				// Adiciona o campo url na foto.
				foreach ($photos as $k => $value) {
					$photos[$k]['url'] = MODULES_PATH.$this->table.'/'.$records[$key]['id'].'/'.$records[$key]['gallery_id'].'/sk_'.$value['id'].$value['extension'];
				}
				$records[$key]['photos'] = $photos;
			}
		}

		// Adiciona o numero de comentários.
		if(in_array('comments_number',$params['include'])){
			$name = get_class($this);
			if(!empty($this->name)) {
				$name = $this->name;
			}
			$recordType = "Modules::".$name;
			$sql = 'SELECT record_id, count(*) as count FROM `core_comments` WHERE record_type = \''.$recordType.'\' AND record_id IN ('.$ids.')  AND approved = 1 GROUP BY `record_id`';
			$counts_comments = $this->connection->find_with_key($sql,'record_id');
			// Seta as tags nos registros como array vazio
			foreach ($records as $key => $value) {
				$records[$key]['comments_number'] = 0;
			}
			foreach ($counts_comments as $key => $value) {
				$records[$key]['comments_number'] = $value['count'];
			}
		}
		return $records;
	}

	/**
	 * Obtém o nome do modelo.
	 *
	 * @access public
	 * @return string
	 */
	public function getModelName() {
		$name = get_class($this);
		if(!empty($this->name)) {
			$name = $this->name;
		}
		return ucfirst(strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $name)));
	}

	/**
	 * Cria a cláusula WHERE aos valores a serem utilizados pelos módulos.
	 *
	 * @access public
	 * @param array $paramWhere
	 * @return string
	 */
	public function addCoreWheres($paramWhere) {
		if (!in_array($this->table, $this->noFlagTables)) {
			foreach ($this->uses as $key) {
				$paramWhere .= " AND ".$this->usesColumns[$key];
			}
		}
		return $paramWhere;
	}

	/**
	 * Procura o primeiro resultado na tabela.
	 *
	 *     $post = new Post();<br/>
	 *     $posts = $post->findFirst(1, {@link findAll() [$params]}<br/>
	 * 
	 * @access public
	 * @param array $param - Parâmetros da Consulta.
	 * @return mixed - Array com os valores do elemento consultado ou false caso não encontre nenhum elemento.
	 */
	public function findFirst($params = array()) {
		$params = array_merge($this->params, $params);
		$params['limit'] = 1;
		$record = $this->findAll($params);
		if(!$record) return false;
		return array_pop($record);
	}

	//TODO Mudar para o local melhor (Classe de banco de dados ou algum helper)
	/**
	 * Protege os dados contra SQL Injection.
	 * 
	 * @access public
	 * @static
	 * @param array $value
	 * @return string
	 */
	public static function protect($value) {
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		if (is_numeric($value)) {
			 return "'".$value."'";
		}
		return "'".mysql_real_escape_string($value)."'";
	}

	/**
	 * Busca um registro.
	 *     $post = new Post();<br/>
	 *     $posts = $post->find(1, {@link findAll() [$params]})<br/>
	 * @access public
	 * @param int|string $id - Código do registro a ser consultado.
	 * @param array $params
	 * @return mixed - Array com os valores do elemento consultado ou false caso não encontre nenhum elemento.
	 */
	public function find($id, $params = array()) {
		$params['where'] = $this->table.".".$this->primaryKey." = ".DrumonModel::protect($id). (!empty($params['where'])? " AND ".$params['where']:"");
		$params['limit'] = 1;
		$record = $this->findAll($params);
		if(!$record) return false;
		return array_pop($record);
	}

	/**
	 * Salva dados no banco de dados.
	 *
	 * Exemplo:
	 *
	 * <code>
	 * $post = new Post();<br/>
	 * $post->save(array('coluna1'=>'valor1','coluna2'=>'valor2'));
	 * </code>
	 *
	 * @access public
	 * @param array $data
	 * @return mixed - int com o número do novo registro, aplicando o método <b>mysql_insert_id()</b> ou false caso nada tenha ocorrido.
	 */
	public function save($data) {
		return $this->connection->save($this->table, $data);
	}

	/**
	 * Adiciona o modelo na query que esteja utilizando behavior'.
	
	 * @ignore
	 * @access public
	 * @param array $params - Parâmetros da query.
	 * @return boolean
	 */
	public function addBehaviorsContent(&$params) {
		// SELECTS
		if (!empty($params['selector'])) {
			return $this->findAllModelsUsingSelector(&$params);
		}
		// TAGS
		if (!empty($params['tags'])) {
			return $this->findAllWithTags(&$params);
		}
		
		return true;
	}

	/**
	 * Filtra valores de arrays multiplos.
	 *
	 * @access private
	 * @param array $array
	 * @param string $index
	 * @param string $value
	 * @return array
	 */
	private function filterByValue ($array, $index, $value) {
		$new_array = array();
		if(is_array($array) && count($array)>0) {
			foreach(array_keys($array) as $key){
				$temp[$key] = $array[$key][$index];
				if ($temp[$key] == $value) {
					$new_array[$key] = $array[$key];
				}
			}
		}
		return $new_array;
	}
	
	/**
	 * Carrega módulo do Drumon a ser usado no controlador.
	 *
	 * @static
	 * @param string $model
	 * @param string $super - Nome do módulo pai, que o módulo está herdando.
	 * @return void
	 */
	public static function load($model, $super = null) {
		if($super === null) {
			$super = $model;
		}
		require ROOT.'/plugins/drumon_model/models/Module'.$super.'.php';
		require ROOT.'/models/'.$model.'.php';
	}
	
	public  function addVisit($id){
		$this->execute('UPDATE '.$this->table.' set visits = visits+1 where id = '.$id);	
	}
	
	
	/**
	 * Incrementa um o valor de um campo
	 *
	 * @param string $id - Id do registro a ser somado.
	 * @param string $field - Nome da coluna do banco.
	 * @param string $quantity - Quantidade a ser incrementado. (default: 1)
	 * @return void
	 */
	public function incrementField($id, $field, $quantity = 1){
		$this->execute('UPDATE '.$this->table.' set '.$field.' = '.$field.'+'.$quantity.' where id = '.$id);	
	}
	
}
?>
