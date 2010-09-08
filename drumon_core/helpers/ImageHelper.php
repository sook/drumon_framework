<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 *
 * Helper para trabalhar com imagens
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class ImageHelper extends Helper {

	/**
	 * Retorna código html da imagem redimensionada.
	 *
	 * @access public
	 * @param String $image - Nome da imagem a ser redimensionanda.
	 * @param String $height - Altura da imagem.
	 * @param String $width - Largura da imagem.
	 * @param String $crop - Local a ser cortado.
	 * @param Array $options - Parâmetros.
	 * @return string - Código html para inclusão de imagens.
	 */
	public function resize($image,$width,$height,$crop = "",$options = array()) {
		$img = '<img src="'.IMAGES_PATH."image.php/".substr($image,strrpos($image,"/"),strlen($image))."?width=".$width."&height=".$height."&cropratio=".$crop."&image=".IMAGES_PATH.$image.'" ';
		foreach ($options as $key => $value) {
			$img .= $key.'="'.$value.'" ';
		}
		$img .= ' />';
		return $img;
	}

	/**
	 * Retorna imagem do site gravatar através do fornecimento do email.
	 *
	 * @access public
	 * @param string $email - Email para verificação de imagem.
	 * @param string $default
	 * @return string - Código html de imagem para visualização de imagem hospedada no gravatar.
	 */
	public function gravatar($email, $default = null) {
		$gravatarMd5 = "";

		$default = ($default != null) ? "?default=".urlencode( $default ) : '';

		if ($email != "" && isset($email)) {
	    $gravatarMd5 = md5($email);
	  }
	//"?default=" . urlencode( $default ) .

		return '<img src="http://www.gravatar.com/avatar/'.$gravatarMd5.$default.'" width="56" alt="Avatar">';
	}
}
?>
