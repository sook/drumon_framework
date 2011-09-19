<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe paginação dos models.
 *
 * @package models
 * @subpackage behaviors
 */
class Paginate extends Behavior implements ArrayAccess, Iterator, Countable {

	/** 
	 * Armazena o total de registros.
	 *
	 * @access public
	 * @var int
	 */
	public $total_records = 0;
	
	/** 
	 * Armazena o valor da página atual.
	 *
	 * @access public
	 * @var string
	 */
	public $current_page;
	
	/** 
	 * Armazena o total de páginas.
	 *
	 * @access public
	 * @var int
	 */
	public $total_pages;
	
	/** 
	 * Armazena o valor de registros por página
	 *
	 * @access public
	 * @var int
	 */
	public $per_page;
	
	/** 
	 * Armazena os registros da página
	 *
	 * @access public
	 * @var mixed
	 */
	public $records;

	/**
	 * Inicia a paginação no modelo 
	 *
	 * @access public
	 * @param int $model - Instancia do model.
	 * @param int $page - Página atual.
	 * @param int $object - Retorna objetos da classe chamada.
	 * @return object - Objeto do tipo página.
	 */
	function page(&$model, $page = 1, $object = false) {
		// Coloca sempre como página 1 se não houver página.
		if(!isset($page) || $page == 0 ) {
			$page = 1;
		}
		
		// Pega o numero de registro por página.
		$this->per_page = empty($this->per_page) ? $model->per_page: $this->per_page;
		
		// Pega os dados da query
		$query_cache = $model->get_query();
		
		// Total de registros no banco do módulo passado com parametro
		$this->total_records = $model->count();

		// Retorna false caso não encontre nada.
		if($this->total_records == 0) {
			$this->records = false;
			return $this;
		}
		// Calcula o total de páginas
		$this->total_pages = ceil($this->total_records / $this->per_page);
		// Altera a página atual
		$this->current_page = $page > $this->total_pages ? $this->total_pages : $page;
		// Verifica de onde irá iniciar a listagem dos registros
		$offset = (($this->current_page-1) * $this->per_page);
		
		// Seta os dados da query
		$model->set_query($query_cache);
		
		// Busca registros
		$this->records = $model->limit($this->per_page)->offset($offset)->all($object);

		return $this;
	}
	
	/**
	 * Set number of records per page
	 *
	 * @param object $model 
	 * @param int $number 
	 * @return object
	 */
	public function per_page(&$model, $number) {
		$this->per_page = $number;
		return $model;
	}

	/**
	 * Retorna a paginação.
	 *
	 * @access public
	 * @return array - Lista de numeração das páginas.
	 */
	function getPages() {
		$pages = array();
		$numberPages = $this->total_pages;
		for($i = 1; $i <= $numberPages; $i++){
			$pages[] = $i;
		}
		return $pages;
	}
	/**
	 * Retorna qual é a próxima página.
	 *
	 * @access public
	 * @return boolean - True se o número de páginas for maior que a página atual.
	 */
	function has_nextPage() {
		if( count($this->getPages()) > $this->current_page) {
			return true;
		}
		return false;
	}

	/**
	 * Verifica a existência de página anterior.
	 *
	 * @access public
	 * @return boolean - True se a pagina atual for maior que 1.
	 */
	function has_prevPage() {
		return ($this->current_page > 1)? true : false;
	}

	/**
	 * Retorna a próxima página.
	 *
	 * @access public
	 * @return int - Valor da próxima página.
	 */
	function getNextPage() {
		return $this->current_page + 1;
	}

	/**
	 * Retorna a página anterior.
	 *
	 * @access public
	 * @return int - Valor da página anterior.
	 */
	function getPrevPage() {
		return $this->current_page - 1;
	}

	/**
	 * Retorna a ultima página.
	 *
	 * @access public
	 * @return int - Valor da ultima página.
	 */
	function getLastPage() {
		$nPagina = count($this->getPages());
		return ($nPagina < 1) ? 1 : $nPagina;
	}

	/**
	 * Verifica a existência de paginação.
	 *
	 * @access public
	 * @return boolean - True se a ultima página for maior que 1, False se não.
	 */
	function has_page() {
		return ($this->getLastPage() > 1) ? true : false;
	}
	
	
	public function offsetExists($offset) {
		return isset($this->records[$offset]);
	}
	
	public function offsetGet($offset) {
		return $this->records[$offset];
	}
	
	public function offsetSet($offset,$value) {
		$this->records[$offset] = $value;
	}
	
	public function offsetUnset($offset) {
		unset($this->records[$offset]);
	}
	
	public function count() {
		return count($this->records);
	}
	
	
	public function rewind() {
		if ($this->records) {
			reset($this->records);
		}
	}

	public function current() {
		return current($this->records);
	}

	public function key() {
		return key($this->records);
	}

	public function next() {
		return next($this->records);
	}

	public function valid() {
		if ($this->records) {
			return key($this->records) !== null;
		} else {
			return false;
		}
	}
}
?>
