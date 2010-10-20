<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */


/**
 * Helper para trabalhar com HTML.
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
 // TODO: Alterar nome da função para padrão CamelCase
class HtmlHelper extends Helper {
	/** 
	 * Armazena o nome dos arquivos css.
	 *
	 * @access private
	 * @var array
	 */
	private $stylesheets = array();
	
	/** 
	 * Armazena o nome dos arquivos javascript.
	 *
	 * @access private
	 * @var array
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
	 * Retorna pasta do módulo passado como valor.
	 *
	 * @access public
	 * @param string $module - Nome do módulo a ser utilizado.
	 * @return string - Url completa da localização do módulo.
	 */
	function module_path($module) {
		return MODULES_PATH.$module;
	}

	/**
	 * Retorna o caminho padrão das imagens concatenado ao nome da imagem.
	 *
	 * @access public
	 * @param string $image - Nome da imagem.
	 * @return string - Caminho para a imagem.
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
	 * @return void|string - String com o código html para adção do arquivo CSS se a opção inline estiver true.
	 */
	function addcss($files, $inline = false, $media = 'all') {
		$files = is_array($files) ? $files : array($files);
		
		$result = '';
		foreach ($files as $file){
			$result .= '<link rel="stylesheet" href="'.STYLESHEETS_PATH.$file.'" type="text/css" media="'.$media.'"/>';
		}
		
		if ($inline) return $result;
		$this->stylesheets[] = $result;
	}

	/**
	 * Retorna o código html dos arquivos css inseridos.
	 *
	 * @access public
	 * @param string|array $files - Nome do(s) arquivo(s) css.
	 * @return string - String com o código html para adção do arquivo CSS.
	 */
	function showcss($files = array()) {
		$files = is_array($files) ? $files : array($files);
		
		$result = '';
		foreach ($files as $file){
			$result.= '<link rel="stylesheet" href="'.STYLESHEETS_PATH.$file.'" type="text/css" media="all"/>';
		}
		
		foreach ($this->stylesheets as $file){
			$result.= $file;
		}
		
		return $result;
	}
	
	/**
	 * Adiciona o arquivo javascript a ser inserido no código HTML.
	 *
	 * @access public
	 * @param string|array $files - Nome do(s) arquivo(s) javascript.
	 * @param boolean $inline - Se true ele retorna o html para inserir o javascript.
	 * @return void|string - String com o código html para adção do arquivo JS se a opção inline estiver true.
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
