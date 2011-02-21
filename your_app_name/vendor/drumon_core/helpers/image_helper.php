<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Helper para trabalhar com imagens
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class ImageHelper extends Helper {

	/**
	 * Retorna a url com a imagem alterada.
	 *
	 * @access public
	 * @param String $image - Nome da imagem a ser redimensionanda.
	 * @param String $height - Altura da imagem.
	 * @param String $width - Largura da imagem.
	 * @param String $crop - Local a ser cortado.
	 * @return string - Url formatada com o tamanho do thumb.
	 */
	public function resize($image, $width, $height, $crop = "") {
		return IMAGES_PATH."image.php/".substr($image,strrpos($image,"/"),strlen($image))."?width=".$width."&height=".$height."&cropratio=".$crop."&image=".$image;
	}

	/**
	 * Retorna url  da imagem do site gravatar através do fornecimento do email.
	 *
	 * @access public
	 * @param string $email - Email para verificação de imagem.
	 * @param string $default
	 * @return string - Url com imagem do gravatar.
	 */
	public function gravatar($email, $default = null) {
		$gravatarMd5 = "";

		$default = ($default != null) ? "?default=".urlencode( $default ) : '';

		if ($email != "" && isset($email)) {
	    $gravatarMd5 = md5($email);
	  }
		//"?default=" . urlencode( $default ) .
		
		return 'http://www.gravatar.com/avatar/'.$gravatarMd5.$default;
	}
	
	/**
	 * Cria uma imagem FAKE com o placehold.it
	 *
	 * @param string $size - Dimensão da imagem, ex. 350x150
	 * @param string $text - Adiciona um texto a imagem.
	 * @param string $color - Seta as cores do fundo e do texto da imagem(hexadecimal), ex. 000/fff
	 * @return string
	 * 
	 */
	public function fake($size='350x150',$text=null,$color=null) {
		$html = '';
		if($text != null) $text = '&text='.str_replace(' ','+',$text);
		$html = '<img src="http://placehold.it/'.$size.'/'.$color.''.$text.'">';
		return $html;
	}
	
}
?>
