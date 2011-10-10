<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */


/**
 * Url Helper
 *
 * @package helpers
 */
class UrlHelper extends Helper {

	/**
	 * Return Full URL path
	 *
	 * @param string $url
	 * @return string
	 */
	public function to($url) {
		return APP_DOMAIN . $url;
	}

	/**
	 * Return active page url
	 *
	 * @return string
	 */
	public function to_here() {
		return $_SERVER['REQUEST_URI'];
	}

	/**
	 * Return image url
	 *
	 * @param string $image
	 * @param $image_path
	 * @return string
	 */
	public function to_image($image, $image_path = IMAGES_PATH) {
		return $image_path . $image;
	}

	/**
	 * Create custom methods on demand. (named routes)
	 *
	 * @param string $name
	 * @param string $arguments
	 * @return string
	 */
	public function __call($name, $arguments) {
		$named_route = str_replace('to_', '', $name);
		if (substr($name,0,3) === 'to_') {
			return APP_DOMAIN . $this->request->url_for($named_route, $arguments);
		} else {
			trigger_error('Method '.$name.' not exist', E_USER_ERROR);
		}
	}


	/**
	 * Retorna pasta do módulo passado como valor.
	 *
	 * @param string $module - Nome do módulo a ser utilizado.
	 * @return string - Url completa da localização do módulo.
	 * @ignore
	 */
	public function module($module) {
		return MODULES_PATH . $module;
	}

}
?>