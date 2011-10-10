<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * List of methods to help work with image in views
 *
 * @package helpers
 */
class ImageHelper extends Helper {

	/**
	 * Return url with timthumb options (http://www.binarymoon.co.uk/projects/timthumb/)
	 *
	 * @param String $image_src
	 * @param String $width
	 * @param String $height
	 * @param array $options extra timthumb options (optional)
	 * @return string
	 */
	public function resize($image_src, $width, $height, $options = array()) {
		$image = IMAGES_PATH . 'thumb.php?src=' . $image_src;

		if (!empty($width)) {
			$image .= "&w=".$width;
		}

		if (!empty($height)) {
			$image .= "&h=".$height;
		}

		foreach ($options as $key => $value) {
			$image .= '&' . $key . '=' . $value;
		}

		return $image;
	}

	/**
	 * Return url image from gravatar service
	 *
	 * @link http://www.gravatar.com
	 *
	 * @param string $email
	 * @param string $default (optional)
	 * @return string
	 */
	public function gravatar($email, $default = null) {
		$gravatarMd5 = "";

		$default = ($default != null) ? "?default=" . urlencode( $default ) : '';

		if ($email != "" && isset($email)) {
	    $gravatarMd5 = md5($email);
	  }

		return 'http://www.gravatar.com/avatar/' . $gravatarMd5 . $default;
	}

	/**
	 * Cria uma imagem FAKE com o placehold.it
	 *
	 * @param string $size image size ex. 350x150
	 * @param string $text text for image (optional)
	 * @param string $color background color / text color. ex. 000/fff (optional)
	 * @return string
	 *
	 */
	public function fake($width = 350, $height = 150,$text=null,$color=null) {
		$html = '';
		if($text != null) $text = '&text='.str_replace(' ','+',$text);
		$html = '<img src="http://placehold.it/'.$width.'x'.$height.'/'.$color.''.$text.'">';
		return $html;
	}
}
?>