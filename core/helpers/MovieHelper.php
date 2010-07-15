<?php  
/**
 * Helper para trabalhar com Vídeos
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class MovieHelper extends SKHelper  {
	
	public $id = NULL;
	public $type = NULL;
	
	public function setId($id) {
		if (preg_match('/([A-Za-z0-9_-]+)/', $url, $matches))
		{
			$this->id = $id;
			return true;
		}elseif (preg_match('/([0-9]+)/', $url, $matches)) {
			$this->id = $id;
			return true;
		}
		else {
			return false;
		}
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function parseUrl($url) {
		if (preg_match('/watch\?v\=([A-Za-z0-9_-]+)/', $url, $matches)){
			$this->type = 'youtube';
			return $matches[1];			
		}
		elseif (preg_match('/([0-9]+)/', $url, $matches)) {
		 	$this->type = 'vimeo';
			return $matches[1];		 	
		} else {	
			return false;
		}
			
	}
	
	public function movie($url = null, $width = 480, $height = 385) {
		if ($url == null) {
			$videoid = $this->id;
		} else {
			$videoid = MovieHelper::parseUrl($url);
			if (!$videoid){
				$videoid = $url;
			}
		} 
		
		if ($this->type == 'youtube') {
		return '<object width="'.$width.'" height="'.$height.'"><param name="movie" value="http://www.youtube.com/v/'.$videoid.'?rel=0&fs=1&loop=0"></param><param name="wmode" value="transparent"></param><param name="allowFullScreen" value="true"><embed src="http://www.youtube.com/v/'.$videoid.'?rel=0&fs=1&loop=0" allowfullscreen="true" type="application/x-shockwave-flash" wmode="transparent" width="'.$width.'" height="'.$height.'"></embed></object>';
		}elseif($this->type == 'vimeo') {
		return '<object width="'.$width.'" height="'.$height.'"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id='.$videoid.'&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id='.$videoid.'&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="'.$width.'" height="'.$height.'"></embed></object>';
		} else {
			return false;
		}
	}	
	
	public static function getClipXml($clip_id) {

		return DomDocument::load('http://vimeo.com/api/clip/' . $clip_id . '.xml');

	}
	
	public static function getClipInfo($clip_id) {

		$clip = MovieHelper::getClipXML($clip_id);
		if (!$clip) return;

		$thumbnail_url = $clip->getElementsByTagName("thumbnail_large")->item(0)->nodeValue;
		// TODO: fetch thumbnail and dimensions
		//$thumbnail = load_image($thumbnail_url);

		$data = array(
			'clip_id' => $clip_id,
			'title' => $clip->getElementsByTagName("title")->item(0)->nodeValue,
			'caption' => $clip->getElementsByTagName("caption")->item(0)->nodeValue,
			'thumbnail_url' => $thumbnail_url,
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

		return $data;

	}
	// $imgid = só recebe valores de 1 a 3
	public function getImgrl($url = null, $imgid = 1) {
		if ($url == null) {
			$videoid = $this->id;
		} else {
			$videoid = MovieHelper::parseUrl($url);
			if (!$videoid){
				$videoid = $url;
			}
		}
		
		if ($this->type == 'youtube') {
			return "http://img.youtube.com/vi/$videoid/$imgid.jpg";
			
		} elseif($this->type == 'vimeo') {
			$vim = MovieHelper::getClipInfo($videoid);
			return $vim['thumbnail_url'];
			
		} else {
			return false;
		}
	}
	public function getImg($url = null, $imgid = 1) {
		return getImgUrl($url, $imgid);
	}
	
	public function showImg($url = null, $imgid = 1, $alt = 'Video screenshot') {
		return "<img src='".$this->GetImgURL($url, $imgid)."' width='130' height='97' border='0' alt='".$alt."' title='".$alt."' />";
	}	
}

$teste = new MovieHelper;
$img = $teste-> getImgrl('http://vimeo.com/8642276');
echo "<img src=\"$img\" width=\"100\"/>";

?>