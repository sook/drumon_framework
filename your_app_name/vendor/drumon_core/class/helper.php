<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Helpers do framework.
 *
 * @package class
 * @abstract
 * @author Sook contato@sook.com.br
 */
abstract class Helper {
	
	
	
	/** 
	 * Língua da aplicação
	 *
	 * @access protected
	 * @var array
	 */
	protected $language;
	
	
	
	/** 
	 * Referência ao objeto request.
	 *
	 * @access protected
	 * @var object
	 */
	protected $request;
	
	/** 
	 * Lista de outros helpers que vão ser utilizados no helper atual.
	 *
	 * @access public 
	 * @var array
	 */
	public $uses = array();
	
	/**
	 * Construtora da Classe.
	 *
	 * @access public
	 * @param array $request - Request
	 * @return void
	 */
	public function __construct($request, $language) {
		$this->request = $request;
		$this->language = $language;
	}

	/**
	 * Substitui os parâmetros de uma string pelos valores de um array de hash.
	 *
	 * @access public
	 * @param string $str - 
	 * @param array $vars -
	 * @param string $char -
	 * @return string - 
	 */
	public function sprintf2($str='', $vars=array(), $char='%') {
	    if (!$str) return '';
	    if (count($vars) > 0) {
	        foreach ($vars as $k => $v) {
	            $str = str_replace($char . $k, $v, $str);
	        }
	    }
	    return $str;
	}
}
?>
