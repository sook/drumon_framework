<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 *
 * Módulo para galeria artística.
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleArtisticGallery extends AppModel {
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo model.
	 *
	 * @access public
	 * @var string
	 */
	public $table = "artistic_galleries";
	
	/** 
	 * Armazena uma lista de funcionalidades que o model irá dispor.
	 *
	 * @access protected
	 * @var array
	 */
	protected $uses = array('trash','status');
	
	/**
	 * Adiciona os comportamentos do modelo.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->imports('Page');
		$this->imports('Selector');
		$this->imports('Tag');
		$this->imports('Comment');
	}
}
?>
