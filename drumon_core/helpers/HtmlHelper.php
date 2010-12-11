<?php
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
	 * @param string $type - Tipo de inserção all, add(default), inline.
	 * @param string $media - Media do stylesheet.
	 * @return void|string - String com o código html para adição do arquivo CSS se a opção for inline.
	 */
	function css($files, $type = 'add', $media = 'all') {
		$files = is_array($files) ? $files : array($files);
		
		$_files = array();
		foreach ($files as $file){
			$_files[] = '<link rel="stylesheet" href="'.STYLESHEETS_PATH.$file.'.css" type="text/css" media="'.$media.'"/>';
		}
		
		if ($type == 'all') {
			$this->stylesheets = array_merge($_files, $this->stylesheets);
			$this->stylesheets = array_unique($this->stylesheets);
			return implode($this->stylesheets);
		} elseif($type == 'inline') {
			return implode($_files);
		} else {
			$this->stylesheets = array_merge($this->stylesheets, $_files);
		}
	}


	/**
	 * Adiciona o arquivo javascript a ser inserido no código HTML.
	 *
	 * @access public
	 * @param string|array $files - Nome do(s) arquivo(s) javascript.
	 * @param string $type - Tipo de inserção all, add(default), inline.
	 * @return void|string - String com o código html para adição do arquivo JS se a opção for inline.
	 */
	function js($files, $type = 'add') {
		$files = is_array($files) ? $files : array($files);

		$_files = array();
		foreach ($files as $file){
			$_files[] = '<script type="text/javascript" src="'.JAVASCRIPTS_PATH.$file.'.js"></script>';
		}
		
		if($type == 'all') {
			$this->javascripts = array_merge($_files, $this->javascripts);
			$this->javascripts = array_unique($this->javascripts);
			return implode($this->javascripts);
		} elseif($type == 'inline') {
			return implode($_files);
		} else {
			$this->javascripts = array_merge($this->javascripts, $_files);
		}
	}
	
	
	/**
	 * Cria um link com os paramentros passados.
	 *
	 * @param string $title - Nome do link
	 * @param string $link - Url de destino do link
	 * @param string $options - Opções extras(confirm, method) e atributos(class,id...).
	 * @return string
	 * 
	 */
	public function link($title, $link, $options = array()) {
		 
		if(array_key_exists('method', $options)) {
			$this->addjs('drumon.'.JS_FRAMEWORK.'.js');
			$options['data-method'] = $options['method'];
			unset($options['method']);
		}
		
		if(array_key_exists('confirm', $options)) {
			$this->addjs('drumon.'.JS_FRAMEWORK.'.js');
			$options['data-confirm'] = $options['confirm'];
			unset($options['confirm']);
		}
		
		$html = '<a href="'.$link.'" '.$this->create_attributes($options).'>'.$title.'</a>';
		return $html;
	}
	
	
	/**
	 * Cria um select com a lista de opções passada.
	 *
	 * @param string $field_name - Nome do campo.
	 * @param array $options_list - Lista de dados dos options.
	 * @param string $options  - Opções extras(selected,include_blank) e atributos(class,id...).
	 * @return string
	 */
	public function select($field_name, $options_list = array(), $options = array()) {
		$defaults = array('include_blank' => false);
		$options = array_merge($defaults,$options);
		
		$html = "";
		$selected = '';
		
		if (isset($options['selected'])) {
			$selected = $options['selected'];
			unset($options['selected']);
		}
		
		if (isset($options['include_blank'])) {
			if($options['include_blank'] === true) {
				$include_blank = '<option></option>';
			}else if($options['include_blank'] === false){
				$include_blank = '';
			}else{
			$include_blank = '<option>'.$options['include_blank'].'</option>';
			}
			unset($options['include_blank']);
		}
		
		$html = '<select name="'.$field_name.'" '.$this->create_attributes($options).'>';
		$html .= $include_blank;
		foreach ($options_list as $key => $value) {
			$selected_on = '';
			if ($selected == $key) {
				$selected_on = 'selected';
			}
			$html .= '<option '.$selected_on.' value="'.$key.'">'.$value.'</option>';
		}
		$html .= '</select>';
		return $html;
	}
	
	
	/**
	 * Exibe um select de html com os anos passados.
	 *
	 * @param string $field_name - Nome do campo.
	 * @param string $start_year - Ano de inicio.
	 * @param string $end_year - Ano de fim.
	 * @param string $options - Veja select para mais detalhes.
	 */
	function select_date_years($field_name,$start_year,$end_year,$options = array()) {
		$defaults = array('selected'=>Date('Y'));
		$options = array_merge($defaults,$options);
		
		$data_list = array();
		for ($i=$start_year; $i <= $end_year; $i++) { 
			$data_list[$i]=$i;
		}
		
		$html = $this->select($field_name,$data_list,$options);
		return $html;
	}
	
	
	/**
	 * Exibe um select de html para com os meses do ano.
	 *
	 * @param string $field_name - Nome do campo.
	 * @param array $options - Veja select para mais detalhes.
	 * @return string
	 */
	function select_date_months($field_name, $options = array()) {
		$defaults = array('selected'=>Date('m'));
		$options = array_merge($defaults,$options);
		
		$data_list = array();
		foreach ($this->locale['date']['months'] as $key => $value) {
			$selected = '';
			$n = ($key < 10) ? '0'.$key : $key ;
			$data_list[$n]=$value;
		}
	
		$html = $this->select($field_name,$data_list,$options);
		return $html;
	}
	
	
	/**
	 * Exibe um select de html para com os dias do mês.
	 *
	 * @param string $field_name - Nome do campo.
	 * @param array $options - Veja select para mais detalhes.
	 * @return string
	 */
	function select_date_days($field_name,$options = array()) {
		$defaults = array('selected'=>Date('d'));
		$options = array_merge($defaults,$options);
		
		$data_list = array();
		for ($i=1; $i <= 31; $i++) {
			$n = ($i < 10) ? '0'.$i : $i ;
			$data_list[$n]=$n;
		}

		$html = $this->select($field_name,$data_list,$options);
		return $html;
	}
	
	
	public function create_attributes($attributes) {
		$attributes_list = array('rel','class','title','id','alt','value','name','data-method','data-confirm');
		$data = "";
		foreach ($attributes as $key => $value) {
			if(in_array($key,$attributes_list)) {
				$data .= ''.$key.'="'.$value.'" ';
			}
		}
		return $data;
	}
	
}
?>
