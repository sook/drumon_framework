<?php
/**
 * Classe responsável por analisar o tempo gasto na execução de uma funcionalidade
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class Benchmark {

	/** 
	 * Instância da classe Benchmark
	 *
	 * @access private
	 * @static 
	 * @name $instance
	 */
	private static $instance;
	
	/**  
	 * Lista com o tempo das funcionalidades (Início e Fim)
	 *
	 * @access private
	 * @name $time
	 */
	private $time = array();

	/**
	 * Construtor
	 *
	 * @access private
	 * @return void
	 */
	private function __construct() { }

	/**
	 * Inicía a contagem de tempo da execução de uma funcionalidade
	 *
	 * @access public 
	 * @static
	 * @param mixed $key Identificador
	 * @return void
	 */
	public static function start($key) {
		$bm = self::getInstance();
		$bm->time[$key]['start'] = microtime(true);
	}

	/**
	 * Para a contagem de tempo da execução de uma funcionalidade
	 * 
	 * @access public
	 * @static
	 * @param mixed $key Identificador
	 * @return string Retorna o tempo total da execução
	 * @see Benchmark::getTime()
	 */
	public static function stop($key) {
		$bm = self::getInstance();
		$bm->time[$key]['stop'] = microtime(true);
		return self::getTime($key);
	}

	/**
	 * Obtém uma listagem dos tempos das funcionalidades (Início e Fim)
	 * 
	 * @static
	 * @access public
	 * @return mixed
	 */
	public static function getListTime() {
		$bm = self::getInstance();
		return $bm->time;
	}

	/**
	 * Obtém o tempo de execução da funcionalidade
	 *
	 * @access public
	 * @static
	 * @param mixed $key Identificador
	 * @return string
	 */
	public static function getTime($key) {
		$bm = self::getInstance();
		return number_format($bm->time[$key]['stop'] - $bm->time[$key]['start'],8);
	}

	/**
	 * Obtém o tempo total de execução presente na listagem de tempos
	 *
	 * @access public
	 * @static	 
	 * @return string
	 */
	public static function getTotals(){
		$bm = self::getInstance();
		$totals = array();
		foreach ($bm->time as $key => $value) {
			$total = number_format($value['stop'] - $value['start'],8);
			$totals[] = $key.': '.$total;
		}
		return $totals;
	}

	/**
	 * Obtém uma instância da classe Benchmark aplicando o padrão de projetos Singleton
	 * 
	 * @access public
	 * @static
	 * @return mixed
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
    		self::$instance = new Benchmark();
		}
		return self::$instance;
	}
}
?>
