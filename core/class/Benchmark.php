<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 *
 * Classe responsável por analisar o tempo gasto na execução de uma funcionalidade.
 *
 * @package class
 * @author Sook contato@sook.com.br
 */
class Benchmark {

	/** 
	 * Instância da classe Benchmark.
	 *
	 * @access private
	 * @static 
	 * @var object
	 */
	private static $instance;
	
	/**  
	 * Lista com o tempo das funcionalidades (Início e Fim).
	 *
	 * @access private
	 * @var array 
	 */
	private $time = array();

	/**
	 * Proibe a reinstância da classe.
	 *
	 * @access private
	 */
	private function __construct() { }

	/**
	 * Inicia a contagem de tempo da execução de uma funcionalidade.
	 *
	 * @access public 
	 * @static
	 * @param string $key - Identificador.
	 * @return void
	 */
	public static function start($key) {
		$bm = self::getInstance();
		$bm->time[$key]['start'] = microtime(true);
	}

	/**
	 * Para a contagem de tempo da execução de uma funcionalidade.
	 * 
	 * @access public
	 * @static
	 * @param string $key - Identificador.
	 * @return string - Tempo total da execução.
	 * @see Benchmark::getTime()
	 */
	public static function stop($key) {
		$bm = self::getInstance();
		$bm->time[$key]['stop'] = microtime(true);
		return self::getTime($key);
	}

	/**
	 * Obtém uma listagem dos tempos das funcionalidades (Início e Fim).
	 * 
	 * @access public
	 * @static
	 * @return array - Lista de tempo das funcionalidades.
	 */
	public static function getListTime() {
		$bm = self::getInstance();
		return $bm->time;
	}

	/**
	 * Obtém o tempo de execução da funcionalidade.
	 *
	 * @access public
	 * @static
	 * @param string $key - Identificador.
	 * @return string - Tempo de execução.
	 */
	public static function getTime($key) {
		$bm = self::getInstance();
		return number_format($bm->time[$key]['stop'] - $bm->time[$key]['start'],8);
	}

	/**
	 * Obtém o tempo total de execução presente na listagem de tempos.
	 *
	 * @access public
	 * @static	 
	 * @return array - Lista de total de execução.
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
	 * Obtém uma instância da classe Benchmark aplicando o padrão de projetos Singleton.
	 * 
	 * @access public
	 * @static
	 * @return object - Instância da classe Benchmark.
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
    		self::$instance = new Benchmark();
		}
		return self::$instance;
	}
}
?>
