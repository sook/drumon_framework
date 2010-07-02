<?
/**
 * Helper's para trabalhar com imagens
 * @author Sook contato@sook.com.br
 * @package default
 */
class ImageHelper extends SKHelper {

	/**
	 * Retorna código html da imagem redimensionada
	 * @access public
	 * @param String $image
	 * @param String $height
	 * @param String $widht
	 * @param String $crop
	 * @param Array $options
	 * @return String
	 */
	public function resize($image,$height,$widht,$crop = "",$options = array()) {
		$img = '<img src="'.IMAGES_PATH."image.php/".substr($image,strrpos($image,"/"),strlen($image))."?width=".$widht."&height=".$height."&cropratio=".$crop."&image=".$image.'" ';
		foreach ($options as $key => $value) {
			$img .= $key.'="'.$value.'" ';
		}
		$img .= ' />';
		return $img;
	}

	/**
	 * Retorna imagem do site gravatar através do fornecimento do email
	 * @access public
	 * @param string $email
	 * @param string $default
	 * @return string
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