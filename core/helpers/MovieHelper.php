<?php
/**
 * Helper para trabalhar com V�deos Youtube e Vimeo.
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class MovieHelper extends SKHelper {
	
	/** 
	 * Armazena a op��o de hospedagem do video (VIMEO ou Youtube)..
	 *
	 * @access public
	 * @var string
	 */
	private $location;
	
	/**
	 * Carrega a rota atrav�s da fun��o getRoute.
	 *
	 * @param string $url Url do v�deo
	 * @access public
	 * @return mixed
	 */
	private function parseUrl($url) {
		if (preg_match('/watch\?v\=([A-Za-z0-9_-]+)/', $url, $matches)) {
			$this->location = 'youtube';
			return $matches[1];
		}
		if (preg_match('/([0-9]+)/', $url, $matches)) {
		 	$this->location = 'vimeo';
			return $matches[1];
		}
		return false;
	}
	
	/**
	 * Retorna um array com par�metros extraidos de um arquivo xml do vimeo.
	 *
	 * @param string $id
	 * @access public
	 * @return array
	 */
	private function getClipInfo($id) {
		$clip = DomDocument::load('http://vimeo.com/api/clip/' . $id . '.xml');
		if (!$clip) return;

		return array(
			'clip_id' => $id,
			'title' => $clip->getElementsByTagName("title")->item(0)->nodeValue,
			'caption' => $clip->getElementsByTagName("caption")->item(0)->nodeValue,
			'thumbnail_url' => $clip->getElementsByTagName("thumbnail_large")->item(0)->nodeValue,
			'thumbnail_width' => 100,
			'thumbnail_height' => 100,
			'width' => $clip->getElementsByTagName("width")->item(0)->nodeValue,
			'height' => $clip->getElementsByTagName("height")->item(0)->nodeValue,
			'duration' => $clip->getElementsByTagName("duration")->item(0)->nodeValue,
			'plays' => $clip->getElementsByTagName("stats_number_of_plays")->item(0)->nodeValue,
			'user_name' => $clip->getElementsByTagName("user_name")->item(0)->nodeValue,
			'user_url' => $clip->getElementsByTagName("user_url")->item(0)->nodeValue,
			'last_updated' => time()
		);
	}

	/**
	 * Retorna um object embed html do v�deo solicitado.
	 *
	 * @param string $url Url para extra��o do identificador do video
	 * @param string $width Largura do object
	 * @param string $height Altura do object
	 * @access public
	 * @return mixed
	 */
	public function movie($url, $width = 480, $height = 385) {
		$id = $this->parseUrl($url);

		if ($this->location == 'youtube') {
			return '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="http://www.youtube.com/v/'.$id.'?rel=0&fs=1&loop=0"></param><param name="wmode" value="transparent"></param><param name="allowFullScreen" value="true"><embed src="http://www.youtube.com/v/'.$id.'?rel=0&fs=1&loop=0" allowfullscreen="true" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'"></embed></object>';
		}
		if($this->location == 'vimeo') {
			return '<object width="'.$width.'" height="'.$height.'"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='.$id.'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id='.$id.'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.$width.'" height="'.$height.'"></embed></object>';
		}		
		return false;
	}
	
	/**
	 * Retorna uma imagem de preview do video
	 *
	 * @param string $url Url para extra��o do identificador do video
	 * @param string $sizeId Id para Preview da imagem s� recebe valores de 1 a 3
	 * @access public
	 * @return mixed
	 */
	public function imageUrl($url, $sizeId = 1) {
		$id = $this->parseUrl($url);
		
		if ($this->location == 'youtube') {
			return "http://img.youtube.com/vi/$id/$sizeId.jpg";
		}
		if($this->location == 'vimeo') {
			$vim = $this->getClipInfo($id);
			return $vim['thumbnail_url'];
		}
		return false;
	}
	
	/**
	 * Retorna o c�digo html da imagem de preview
	 *
	 * @param string $url Url para extra��o do identificador do video
	 * @param string $sizeId Id para preview da imagem s� recebe valores de 1 a 3
	 * @param string $width Largura do preview
	 * @param string $height Altura do preview
	 * @param string $alt Par�metro html alt do preview
	 * @access public
	 * @return string
	 */
	public function showImage($url, $sizeId = 1, $width = 130, $height = 97, $alt = 'Video screenshot') {
		return "<img src='".$this->imageUrl($url, $sizeId)."' width='".$width."' height='".$height."' border='0' alt='".$alt."' title='".$alt."' />";
	}
}
?>
