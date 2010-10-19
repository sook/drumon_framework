<?
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
	function self() {
		return $_SERVER['REQUEST_URI'];
	}
}
?>
