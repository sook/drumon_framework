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
	
	var $uses = array('Text');
	
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
	 * Armazena os blocos de html a serem usados na aplicação.
	 *
	 * @access private
	 * @var array
	 */
	private $blocks = array();
	
	
	/**
	 * Imprimi ou inseri blocos de códigos.
	 *
	 * @return void|string
	 */
	public function block() {
		if (func_num_args() > 1) {
			$this->blocks[func_get_arg(0)][] = func_get_arg(1);
		} else {
			if (isset($this->blocks[func_get_arg(0)])) {
				return implode($this->blocks[func_get_arg(0)]);
			}
		}
	}

	/**
	 * Adiciona o arquivo css a ser inserido no código HTML.
	 *
	 * @access public
	 * @param string|array $files - Nome do(s) arquivo(s) css.
	 * @param string $type - Tipo de inserção show, add(default), inline.
	 * @param string $media - Media do stylesheet.
	 * @return void|string - String com o código html para adição do arquivo CSS se a opção for inline.
	 */
	public function css($files, $type = 'add', $media = 'all') {
		$files = is_array($files) ? $files : array($files);
		
		$_files = array();
		foreach ($files as $file){
			if (!empty($file)) {
				$_files[] = '<link rel="stylesheet" href="'.STYLESHEETS_PATH.$file.'.css" type="text/css" media="'.$media.'"/>';
			}
		}
		
		if ($type == 'header-only') {
			$this->stylesheets = array_unique(array_merge($_files, $this->stylesheets));
			return implode("\n",$this->stylesheets);
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
	 * @param string $type - Tipo de inserção show, add(default), inline.
	 * @return void|string - String com o código html para adição do arquivo JS se a opção for inline.
	 */
	public function js($files, $type = 'add') {
		$files = is_array($files) ? $files : array($files);

		$_files = array();
		foreach ($files as $file){
			$_files[] = '<script type="text/javascript" src="'.JAVASCRIPTS_PATH.$file.'.js"></script>';
		}
		
		if($type == 'header-only') {
			$this->javascripts = array_merge($_files, $this->javascripts);
			$this->javascripts = array_unique($this->javascripts);
			return implode("\n",$this->javascripts);
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
			$this->js('vendor/drumon-'.JS_FRAMEWORK);
			$options['data-method'] = $options['method'];
			unset($options['method']);
		}
		
		if(array_key_exists('confirm', $options)) {
			$this->js('vendor/drumon-'.JS_FRAMEWORK);
			$options['data-confirm'] = $options['confirm'];
			unset($options['confirm']);
		}
		
		$html = '<a href="'.$link.'" '.$this->create_attributes($options).'>'.$title.'</a>';
		return $html;
	}
	
	
	/**
	 * Gera a tag form com os dados necessários para o drumon.
	 *
	 * @param string $url - Destino da requisição do form.
	 * @param string $options - Atributos(class,id...).
	 * @return string
	 */
	public function form($url, $options = array()) {
		$options = array_merge(array('method' => 'post', 'file' => false), $options);
		$html = array();
		
		if (strtolower($options['method']) === 'get') {
			$real_method = 'get';
			$inputs = '';
		}else{
			$real_method = 'post';
			$inputs = '<input type="hidden" name="_token" value="'.REQUEST_TOKEN.'">';
			$inputs .= '<input type="hidden" name="_method" value="'.$options['method'].'">';
		}
		
		if ($options['file']) {
			$options['enctype'] = "multipart/form-data";
		}
		
		$html[] = '<form action="'.$url.'" method="'.$real_method.'" '.$this->create_attributes($options).'>';
		$html[] = $inputs;
		
		return implode($html);
	}
	
	
	/**
	 * Atalho para a tag de form fechada. </form>
	 *
	 * @return string
	 */
	public function form_end() {
		return '</form>';
	}
	
	
	// melhorar
	public function value($field,$data) {
		if(isset($data[$field])) return $data[$field];
		return '';
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
				$include_blank = '<option value=""></option>';
			}else if($options['include_blank'] === false){
				$include_blank = '';
			}else{
			$include_blank = '<option value="">'.$options['include_blank'].'</option>';
			}
			unset($options['include_blank']);
		}
		
		$html = '<select name="'.$field_name.'" '.$this->create_attributes($options).'>';
		$html .= $include_blank;
		foreach ($options_list as $key => $value) {
			$selected_on = '';
			if ( (string) $selected === (string) $key) {
				$selected_on = ' selected';
			}
			$html .= '<option'.$selected_on.' value="'.$key.'">'.$value.'</option>';
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
	public function select_date_years($field_name, $start_year, $end_year, $options = array()) {
		$defaults = array('selected'=>Date('Y'));
		$options = array_merge($defaults,$options);
		
		$data_list = array();
		for ($i=$start_year; $i <= $end_year; $i++) { 
			$data_list[$i]=$i;
		}
		
		$html = $this->select($field_name, $data_list, $options);
		return $html;
	}
	
	
	/**
	 * Exibe um select de html para com os meses do ano.
	 *
	 * @param string $field_name - Nome do campo.
	 * @param array $options - Veja select para mais detalhes.
	 * @return string
	 */
	public function select_date_months($field_name, $options = array()) {
		$defaults = array('selected'=>Date('m'));
		$options = array_merge($defaults,$options);
		
		$data_list = array();
		foreach ($this->text->translate('date.months') as $key => $value) {
			$selected = '';
//			$n = ($key < 10) ? '0'.$key : $key ;
			$data_list[$key]=$value;
		}
	
		$html = $this->select($field_name, $data_list, $options);
		return $html;
	}
	
	
	/**
	 * Exibe um select de html para com os dias do mês.
	 *
	 * @param string $field_name - Nome do campo.
	 * @param array $options - Veja select para mais detalhes.
	 * @return string
	 */
	public function select_date_days($field_name, $options = array()) {
		$defaults = array('selected' => Date('d'));
		$options = array_merge($defaults,$options);
		
		$data_list = array();
		for ($i=1; $i <= 31; $i++) {
			$n = ($i < 10) ? '0'.$i : $i ;
			$data_list[$i]=$n;
		}

		$html = $this->select($field_name, $data_list, $options);
		return $html;
	}
	
	public function show_select_date($field_name, $options = array()) {
		$options = array_merge(array('start_year'=>Date('Y')-120,'end_year'=>Date('Y')),$options);
		
		$html = $this->select_date_days($field_name.'[day]',$options);
		$html .= $this->select_date_months($field_name.'[month]',$options);
		$html .= $this->select_date_years($field_name.'[year]',$options['start_year'],$options['end_year'],$options);
		return $html;
	}
	
	
	public function create_attributes($attributes) {
		$attributes_list = array('rel','class','title','id','alt','value','name','data-method','data-confirm','enctype');
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
