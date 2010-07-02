<?
/**
 * Helper's para trabalhar com HTML
 * @author Sook contato@sook.com.br
 * @package default
 */
class HtmlHelper extends SKHelper {

	private $styleSheets = array();
	private $javascripts = array();
	/**
	 * Retorna a url incremntada de alguma String
	 * @access public
	 * @param String $v
	 * @return String
	 */
	function url($v){
		return APP_URL.$v;
	}

	/**
	 * Gera uma tag Link em HTML
	 * @access public
	 * @return String
	 */
	function link() {
		$args = func_get_args();

		$title = isset($args[2]['title']) ? $args[2]['title']: '';
		$id = isset($args[2]['id']) ? $args[2]['id']: '';
		$class = isset($args[2]['class']) ? $args[2]['class']: '';
		$rel = isset($args[2]['rel']) ? $args[2]['rel']: '';

		return '<a id="'.$id.'" class="'.$class.'" rel="'.$rel.'" href="'.$args[1].'" title="'.$title.'">'.$args[0].'</a>';
	}

	/**
	 * Retorna pasta de módulos
	 * @access public
	 * @param String $v
	 * @return String
	 */
	function module_path($v){
		return MODULES_PATH.$v;
	}

	/**
	 * Retorna pasta de imagens
	 * @access public
	 * @param String $v
	 * @return String
	 */
	function image_path($v){
		return IMAGES_PATH.$v;
	}

	/**
	 * Retorna a listagem em html dos arquivos css
	 * @access public
	 * @param Array $files
	 * @param Boolean $inline
	 * @return Array
	 */
	function addcss($files, $inline = false) {
		$files = is_array($files) ? $files : array($files);

		$result = "";
		if ($inline) {
			foreach ($this->files as $file){
				$result.= '<link rel="stylesheet" href="'.CSS_PATH.$file.'" type="text/css" media="all"/>';
			}
			return result;
		}

		$this->styleSheets = array_merge($this->styleSheets, $files);
	}

	/**
	 * Insere o código html para inserção dos arquivos css
	 * @access public
	 * @param Array $files
	 * @return String
	 */
	// Print the css on page.
	function showcss($files) {
		$files = is_array($files) ? $files : array($files);
		$result = '';
		$this->styleSheets = array_merge($files, $this->styleSheets);
		foreach ($this->styleSheets as $file){
			$result.= '<link rel="stylesheet" href="'.CSS_PATH.$file.'" type="text/css" media="all"/>';
		}
		return $result;
	}

	// Insert stylesheets files.
	// @deprecated
	function css($files) {
		$this->addcss($files);
	}

	// Print the stylesheets on page.
	// @deprecated
	function styleSheets($files) {
		return $this->showcss($files);
	}

	// Insert javascript files
	// @deprecated
	function js($files) {
		$this->addjs($files,true);
	}


	function addjs($files, $inline = false) {
		$files = is_array($files) ? $files : array($files);

		$result = "";
		if ($inline) {
			foreach ($files as $file){
				$result.= '<script type="text/javascript" src="'.JAVASCRIPTS_PATH.$file.'"></script>';
			}
			return $result;
		}

		$this->javascripts = array_merge($this->javascripts, $files);
	}

	// Print the css on page.
	function showjs($files = array()) {
		$files = is_array($files) ? $files : array($files);
		$result = '';
		$this->javascripts = array_merge($files, $this->javascripts);
		foreach ($this->javascripts as $file){
			$result.= '<script type="text/javascript" src="'.JAVASCRIPTS_PATH.$file.'"></script>';
		}
		return $result;
	}
}
?>