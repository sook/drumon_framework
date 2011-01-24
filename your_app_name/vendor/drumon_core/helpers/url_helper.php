<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */


/**
 * Helper para trabalhar com URL.
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class UrlHelper extends Helper {
	
	/**
	 * Retorna o caminho completo de uma url.
	 *
	 * @access public
	 * @param string $url - Caminho parcial da url.
	 * @return string - Caminho completo da url.
	 */
	function to() {
		if(func_num_args() === 2){
			if(func_get_arg(0) == 'image'){
				return $this->image(func_get_arg(1));
			}
		}else{
			return APP_DOMAIN.func_get_arg(0);
		}
	}
	
	
	/**
	 * Create custom methods on demand.
	 *
	 * @param string $name 
	 * @param string $arguments 
	 * @return string
	 */
	public function __call($name, $arguments) {
		$named_route = str_replace('to_','',$name);
		if(substr($name,0,3) === 'to_') {
			return APP_DOMAIN.$this->request->url_for($named_route,$arguments);
		}else{
			trigger_error('Method '.$name.' not exist');
		}
	}


	/**
	 * Retorna pasta do módulo passado como valor.
	 *
	 * @access public
	 * @param string $module - Nome do módulo a ser utilizado.
	 * @return string - Url completa da localização do módulo.
	 */
	function module($module) {
		return MODULES_PATH.$module;
	}

	/**
	 * Retorna o caminho padrão das imagens concatenado ao nome da imagem.
	 *
	 * @access public
	 * @param string $image - Nome da imagem.
	 * @return string - Caminho para a imagem.
	 */
	function image($image) {
		return IMAGES_PATH.$image;
	}
	
	/**
	 * Retorna o caminho da página atual
	 *
	 * @access public
	 * @return string
	 */
	function to_here() {
		return $_SERVER['REQUEST_URI'];
	}
}
?>
