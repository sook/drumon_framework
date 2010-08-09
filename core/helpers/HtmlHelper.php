<?
/**
 * Helper para trabalhar com HTML
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
 // TODO: Alterar nome da função para padrão CamelCase
class HtmlHelper extends SKHelper {
	/** 
	 * Armazena o nome dos arquivos css
	 *
	 * @access private
	 * @name $styleSheets
	 */
	private $styleSheets = array();
	
	/** 
	 * Armazena o nome dos arquivos javascript
	 *
	 * @access private
	 * @name $javascripts
	 */
	private $javascripts = array();
	
	/**
	 * Retorna a url incremntada de alguma string
	 *
	 * @access public
	 * @param string $v
	 * @return string
	 */
	function url($v){
		return APP_DOMAIN.$v;
	}

	/**
	 * Gera uma tag Link em HTML
	 *
	 * @access public
	 * @return string
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
	 *
	 * @access public
	 * @param string $v
	 * @return string
	 */
	function module_path($v){
		return MODULES_PATH.$v;
	}

	/**
	 * Retorna pasta de imagens
	 *
	 * @access public
	 * @param string $v
	 * @return string
	 */
	function image_path($v){
		return IMAGES_PATH.$v;
	}

	/**
	 * Adiciona o arquivo css a ser inserido no código HTML
	 *
	 * @access public
	 * @param array $files
	 * @param boolean $inline
	 * @return array
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
	 * Retorna o código html dos arquivos css inseridos
	 *
	 * @access public
	 * @param array $files
	 * @return string
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
	
	/**
	 * Adiciona o arquivo javascript a ser inserido no código HTML
	 *
	 * @access public
	 * @param array $files
	 * @param boolean $inline
	 * @return string
	 */
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

	/**
	 * Retorna o código html dos arquivos javascripts inseridos
	 *
	 * @access public
	 * @param array $files
	 * @return string
	 */
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
