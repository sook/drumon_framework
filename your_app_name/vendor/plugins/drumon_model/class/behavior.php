<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe para construção de Model's
 * @package models
 * @subpackage behaviors
 */
abstract class Behavior {
	/** 
	 * Armazena o nome do modelo
	 *
	 * @access protected
	 * @var string
	 */
	protected $model;

	/**
	 * Instancia o model
	 *
	 * @param string $model Modelo
	 * @access public
	 * @return void
	 */
	public function __construct() {
		//$this->model = &$model;
	}
}
?>
