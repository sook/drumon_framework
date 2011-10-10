<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Main Helper class
 *
 * @package class
 */
abstract class Helper {
	
	/** 
	 * Request object
	 *
	 * @var Request
	 */
	protected $request;
	
	/** 
	 * List helpers used on another helper
	 *
	 * @var array
	 */
	public $uses = array();
	
	/**
	 * Setup class
	 *
	 * @param object $request Request object
	 * @return void
	 */
	public function __construct(&$request) {
		$this->request = $request;
	}

	/**
	 * Replace string params with hash array values
	 *
	 * @deprecated
	 * @param string $str
	 * @param array $vars
	 * @param string $char
	 * @return string - 
	 */
	public function sprintf2($str = '', $vars = array(), $char = '%') {
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