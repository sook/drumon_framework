<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Módulo para vídeo
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class SiteConfig extends DrumonModel {
	/** 
	 * Armazena o nome da tabela a ser utilizada pelo módulo
	 *
	 * @access public
	 * @var string
	 */
	public $table = "core_metadata";
	
	/**
	 * Adiciona os comportamentos do módulo
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}
	
	public static function get($key){
		$consult = "SELECT value FROM core_metadata WHERE `module_alias` = 'site_config' AND `key` = 'data_for_".$key."'";
		$conn = Database::get_instance();
		$result =  $conn->find($consult);
		return $result[0]['value'];
	}
}
?>
