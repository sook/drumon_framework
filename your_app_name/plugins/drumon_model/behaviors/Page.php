<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe paginação dos models.
 *
 * @author Sook contato@sook.com.br
 * @package models
 * @subpackage behaviors
 */
class Page extends ModelBehavior {

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
	 * Armazena o resultado de uma consulta
	 *
	 * @access public
	 * @var mixed
	 */
	public $results;

	/**
	 * .
	 *
	 * @access public
	 * @param int $pages - Quantidade de páginas.
	 * @param array $params - Parâmetros a serem utilizados pela cláusula WHERE.
	 * @return object - Objeto do tipo página.
	 */
	function paginate($page = 1, $params = array()) {
		if(!isset($page) || $page == 0 ) {
			$page = 1;
		}
		// Pega o numero de registro por página.
		$this->per_page = empty($params['per_page']) ? $this->model->per_page: $params['per_page'];
		$params = array_merge($this->model->params, $params);
		$this->model->addBehaviorsContent(&$params);

		// Total de registros no banco do módulo passado com parametro
		$total_records = $this->model->query("SELECT COUNT(*) as count_all FROM ".$this->model->table." WHERE ".$this->model->addCoreWheres($params['where']));
		$this->total_records = $total_records[0]['count_all'];

		if($this->total_records == 0) {
			$this->results = false;
			return $this;
		}

		// Calcula o total de páginas
		$this->total_pages = ceil($this->total_records / $this->per_page);

		// Altera a página de visualização
		$page = $page > $this->total_pages ? $this->total_pages : $page;
		$this->current_page = $page;

		// Verifica de onde irá iniciar a listagem dos registros
		$from = (($this->current_page-1) * $this->per_page);

		// Consulta os registros de acordo com o limit.
		$params['limit'] = $from.",".$this->per_page;
		// Busca registros
		$this->results = $this->model->findAll($params);

		return $this;
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
	function hasNextPage() {
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
	function hasPrevPage() {
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
	function hasPage() {
		return ($this->getLastPage() > 1) ? true : false;
	}
}
?>