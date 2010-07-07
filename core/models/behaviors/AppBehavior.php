<?php
/**
 * Classe para construção de Model's
 * @author Sook contato@sook.com.br
 * @package models
 * @subpackage behaviors
 */
abstract class AppBehavior {
	/** 
	 * Armazena o nome do modelo
	 *
	 * @access protected
	 * @name $model
	 */
	protected $model;

	/**
	 * Instancia o model
	 *
	 * @param string $model Modelo
	 * @access public
	 * @return void
	 */
	public function __construct($model) {
		$this->model = $model;
	}
}
?>
