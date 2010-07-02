<?
/**
 * Classe paginação dos model's
 * @author Sook contato@sook.com.br
 * @package models
 * @subpackage behaviors
 */
class Page extends AppBehavior {

	/** 
	 * Armazena o total de registros
	 *
	 * @access public
	 * @name $totalRecords
	 */
	public $totalRecords = 0;
	
	/** 
	 * Armazena o valor da página atual
	 *
	 * @access public
	 * @name $currentPage
	 */
	public $currentPage;
	
	/** 
	 * Armazena o total de páginas
	 *
	 * @access public
	 * @name $totalPages
	 */
	public $totalPages;
	
	/** 
	 * Armazena o valor de registros por página
	 *
	 * @access public
	 * @name $perPage
	 */
	public $perPage;
	
	/** 
	 * Armazena o resultado de uma consulta
	 *
	 * @access public
	 * @name $results
	 */
	public $results;

	/**
	 * Procura comentários
	 * @access public
	 * @param integer $pages
	 * @param array $params
	 * @return array
	 */
	function paginate($page = 0, $params = array()) {
		if(!isset($page)) $page = 1;
		// Pega o numero de registro por página.
		$this->perPage = empty($params['perPage']) ? $this->model->perPage: $params['perPage'];
		$params = array_merge($this->model->params, $params);
		$this->model->addBehaviorsContent(&$params);


		// Total de registros no banco do módulo passado com parametro
		$totalRecords = $this->model->query("SELECT COUNT(*) as count_all FROM ".$this->model->table." WHERE ".$this->model->getStringWhere($params['where']));
		$this->totalRecords = $totalRecords[0]['count_all'];

		if($this->totalRecords == 0) {
			$this->results = false;
			return $this;
		}

		// Calcula o total de páginas
		$this->totalPages = ceil($this->totalRecords / $this->perPage);

		// Altera a página de visualização
		$page = $page > $this->totalPages ? $this->totalPages : $page;
		$this->currentPage = $page;

		// Verifica de onde irá iniciar a listagem dos registros
		$from = (($this->currentPage-1) * $this->perPage);

		// Consulta os registros de acordo com o limit.
		$params['limit'] = $from.",".$this->perPage;

		// Busca registros
		$this->results = $this->model->findAll($params);

		return $this;
	}

	/**
	 * Carrega páginas
	 * @access public
	 * @return array
	 */
	function getPages() {
		$pages = array();
		$numberPages = $this->totalPages;
		for($i = 1; $i <= $numberPages; $i++){
			$pages[] = $i;
		}
		return $pages;
	}
	/**
	 * Retorna qual é a próxima página
	 * @access public
	 * @return boolean
	 */
	function hasNextPage() {
		if( count($this->getPages()) > $this->currentPage) {
			return true;
		}
		return false;
	}

	/**
	 * Verifica a existência de página anterior
	 * @access public
	 * @return boolean
	 */
	function hasPrevPage() {
		return ($this->currentPage > 1)? true : false;
	}

	/**
	 * Retorna a próxima página
	 * @access public
	 * @return integer
	 */
	function getNextPage() {
		return $this->currentPage + 1;
	}

	/**
	 * Retorna a página anterior
	 * @access public
	 * @return integer
	 */
	function getPrevPage() {
		return $this->currentPage - 1;
	}

	/**
	 * Retorna a ultima página
	 * @access public
	 * @return integer
	 */
	function getLastPage() {
		$nPagina = count($this->getPages());
		return ($nPagina < 1) ? 1 : $nPagina;
	}

	/**
	 * Verifica a existência de paginação
	 * @access public
	 * @return array
	 */
	function hasPage() {
		return ($this->getLastPage() > 1) ? true : false;
	}
}
?>
