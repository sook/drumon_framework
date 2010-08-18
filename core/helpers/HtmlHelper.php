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
	 * Armazena o nome dos arquivos javascript.
	 *
	 * @access private
	 * @name $javascripts
	 */
	private $javascripts = array();
	
	/**
	 * Retorna o caminho completo de uma url.
	 *
	 * @access public
	 * @param string $url - Caminho parcial da url.
	 * @return string - Caminho completo da url.
	 */
	function url($url) {
		return APP_DOMAIN.$url;
	}

	/**
	 * Gera uma tag link em HTML dos parametros passados.
	 * 
	 * Exemplo:
	 *
	 * <code>
	 * $html->link('Drumon','http://www.drumon.com.br',array('class'=>'blue'));
	 * </code>
	 *
	 * @access public
	 *
	 * @param string $text - Texto do link.
	 * @param string $url - Url de destindo do link.
	 * @param array $options (opcional) <br>
	 * title => Atributo title da tag link <br>
	 * id => Atributo id da tag link <br>
	 * class => Atributo class da tag link <br>
	 * rel => Atributo rel do link
	 *
	 * @return string - O elemento html de link montado.
	 */
	function link() {
		$args = func_get_args();

		$title = isset($args[2]['title']) ? $args[2]['title']: '';
		$id = isset($args[2]['id']) ? $args[2]['id']: '';
		$class = isset($args[2]['class']) ? $args[2]['class']: '';
		$rel = isset($args[2]['rel']) ? $args[2]['rel']: '';

		return '<a id="'.$id.'" class="'.$class.'" rel="'.$rel.'" href="'.APP_DOMAIN.$args[1].'" title="'.$title.'">'.$args[0].'</a>';
	}

	/**
	 * Retorna pasta do módulo passado como valor.
	 *
	 * @access public
	 * @param string $modulo - Nome do módulo.
	 * @return string - Caminho para o módulo
	 */
	function module_path($module) {
		return MODULES_PATH.$module;
	}

	/**
	 * Retorna o caminho da imagem de acordo com o valor setado em IMAGES_PATH.
	 *
	 * @access public
	 * @param string $image - Nome da imagem.
	 * @return string - Caminho para a imagem
	 */
	function image_path($image) {
		return IMAGES_PATH.$image;
	}

	/**
	 * Adiciona o arquivo css a ser inserido no código HTML.
	 *
	 * @access public
	 * @param string|array $files - Nome do(s) arquivo(s) css.
	 * @param boolean $inline - Se true ele retorna o html para inserir o css.
	 * @return void|string
	 */
	function addcss($files, $inline = false) {
		$files = is_array($files) ? $files : array($files);

		$result = '';
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
	 * @param string|array $files - Nome do(s) arquivo(s) css.
	 * @return string
	 */
	// Print the css on page.
	function showcss($files = array()) {
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
	 * @param string|array $files - Nome do(s) arquivo(s) javascript.
	 * @param boolean $inline - Se true ele retorna o html para inserir o javascript.
	 * @return void|string
	 */
	function addjs($files, $inline = false) {
		$files = is_array($files) ? $files : array($files);

		$result = '';
		if ($inline) {
			foreach ($files as $file){
				$result.= '<script type="text/javascript" src="'.JAVASCRIPTS_PATH.$file.'"></script>';
			}
			return $result;
		}

		$this->javascripts = array_merge($this->javascripts, $files);
	}

	/**
	 * Retorna o código html dos arquivos javascripts passados como parametro.
	 *
	 * @access public
	 * @param mixed $files - Nome do(s) arquivo(s) javascript.
	 * @return string - Html da lista de javascripts.
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