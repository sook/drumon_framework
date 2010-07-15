<?php
/**
 * Helper para trabalhar com Vídeos
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class MovieHelper extends SKHelper {
	
	private $location;
	
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
	
	// $imgid = só recebe valores de 1 a 3
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

	public function showImage($url, $sizeId = 1, $width = 130, $height = 97, $alt = 'Video screenshot') {
		return "<img src='".$this->imageUrl($url, $sizeId)."' width='".$width."' height='".$height."' border='0' alt='".$alt."' title='".$alt."' />";
	}
}
?>
