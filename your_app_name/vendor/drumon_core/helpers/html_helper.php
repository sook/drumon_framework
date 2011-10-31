<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */


/**
 * Helper to work with HTML
 *
 * @package helpers
 */

class HtmlHelper extends Helper {

	/**
	 * Stylesheets list
	 *
	 * @var array
	 */
	private $stylesheets = array();

	/**
	 * Javascripts list
	 *
	 * @var array
	 */
	private $javascripts = array();

	/**
	 * List of text blocks
	 *
	 * @var array
	 */
	private $blocks = array();


	/**
	 * Add or get code blocks
	 *
	 * Example:
	 *
	 * $this->block('meta','php,code,html');
	 *
	 * $this->block('meta'); // => php,code,html
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
	 * Add css to view
	 *
	 * @param $files
	 * @param $block
	 * @param $media
	 *
	 * @return void|string
	 */
	public function css($files, $block = 'header', $media = 'all') {
		$files = is_array($files) ? $files : array($files);

		$_files = array();
		foreach ($files as $file){
			if (!empty($file)) {
				$_files[] = '<link rel="stylesheet" href="'.STYLESHEETS_PATH.$file.'.css" type="text/css" media="'.$media.'"/>';
			}
		}

		if($block == 'inline') {
			return implode("\n",$_files);
		} else {
			if (!isset($this->stylesheets[$block])) {
				$this->stylesheets[$block] = array();
			}
			$this->stylesheets[$block] = array_merge($this->stylesheets[$block], $_files);
		}
	}

	/**
	 * Print stylessheets on view
	 *
	 * @param $files
	 * @param $block
	 * @param media
	 * @return string
	 */
	public function styles($files, $block = 'header', $media = 'all') {

		$files = is_array($files) ? $files : array($files);

		$_files = array();
		foreach ($files as $file) {
			if (!empty($file)) {
				$_files[] = '<link rel="stylesheet" href="'.STYLESHEETS_PATH.$file.'.css" type="text/css" media="'.$media.'"/>';
			}
		}

		if (!isset($this->stylesheets[$block])) {
			$this->stylesheets[$block] = array();
		}

		$this->stylesheets[$block] = array_unique(array_merge($_files, $this->stylesheets[$block]));
		return implode("\n", $this->stylesheets[$block]);
	}

	/**
	 * Add javascripts to view
	 *
	 * @param $files
	 * @param $block
	 * @return void|string
	 */
	public function js($files, $block = 'header') {
		$files = is_array($files) ? $files : array($files);

		$_files = array();
		foreach ($files as $file){
			$_files[] = '<script type="text/javascript" src="'.JAVASCRIPTS_PATH.$file.'.js"></script>';
		}

		if($block == 'inline') {
			return implode("\n",$_files);
		} else {
			if (!isset($this->javascripts[$block])) {
				$this->javascripts[$block] = array();
			}
			$this->javascripts[$block] = array_merge($this->javascripts[$block], $_files);
		}
	}

	/**
	 * Print javascripts in view
	 *
	 * @param $files
	 * @param $block
	 * @return string
	 */
	public function scripts($files, $block = 'header') {

		$files = is_array($files) ? $files : array($files);

		$_files = array();
		foreach ($files as $file) {
			if (!empty($file)) {
				$_files[] = '<script type="text/javascript" src="'.JAVASCRIPTS_PATH.$file.'.js"></script>';
			}
		}

		if (!isset($this->javascripts[$block])) {
			$this->javascripts[$block] = array();
		}

		$this->javascripts[$block] = array_unique(array_merge($_files, $this->javascripts[$block]));
		return implode("\n", $this->javascripts[$block]);
	}

	/**
	 * Create a html link
	 *
	 * @param string $title Link text
	 * @param string $url
	 * @param string $options Extra options(confirm, method) and html attributes(class,id...).
	 *
	 * @return string
	 *
	 */
	public function link($title, $url, $options = array()) {

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

		$html = '<a href="'.$url.'" '.$this->create_attributes($options).'>'.$title.'</a>';
		return $html;
	}

	/**
	 * Create html form tag
	 *
	 * @param string $url
	 * @param string $options Html attributes(class,id...).
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
	 * Shortcut for close form tag </form>
	 *
	 * @return string
	 */
	public function form_end() {
		return '</form>';
	}


	/**
	 * Get input value
	 */
	public function value($field, $data) {

		if (is_array($data)) {
			if (isset($data[$field])) return $data[$field];
		}

		if (isset($data->$field)) return $data->$field;

		return '';
	}


	/**
	 * Create a html select
	 *
	 * @param string $field_name
	 * @param array $options_list
	 * @param string $options Extras options(selected,include_blank) and html attributes(class,id...)
	 * @return string
	 */
	public function select($field_name, $options_list = array(), $options = array()) {
		$defaults = array('include_blank' => false);
		$options = array_merge($defaults,$options);

		$html = "";
		$selected = '';
		$disables = array();

		if (isset($options['selected'])) {
			$selected = $options['selected'];
			unset($options['selected']);
		}

		if (isset($options['disabled'])) {
			$disables = is_array($options['disabled']) ? $options['disabled'] : array($options['disabled']);
			unset($options['disabled']);
		}

		if (isset($options['include_blank'])) {
			if ($options['include_blank'] === true) {
				$include_blank = '<option value=""></option>';
			} else if ($options['include_blank'] === false) {
				$include_blank = '';
			} else {
				$include_blank = '<option value="">'.$options['include_blank'].'</option>';
			}
			unset($options['include_blank']);
		}

		$html = '<select name="'.$field_name.'" '.$this->create_attributes($options).'>';
		$html .= $include_blank;
		foreach ($options_list as $key => $value) {
			$selected_on = '';
			if ((string) $selected === (string) $key) {
				$selected_on = ' selected';
			}
			$disabled = '';
			if (in_array($key, $disables)) {
				$disabled = ' disabled="disabled"';
			}
			$html .= '<option'.$selected_on.' value="'.$key.'"'.$disabled.'>'.$value.'</option>';
		}
		$html .= '</select>';

		return $html;
	}


	/**
	 * Get html select with a range of years
	 *
	 * @param string $field_name
	 * @param string $start_year
	 * @param string $end_year
	 * @param string $options @see HtmlHelper::select
	 * @return string
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
	 * Get html select with all months
	 *
	 * @param string $field_name
	 * @param array $options @see HtmlHelper::select
	 * @return string
	 */
	public function select_date_months($field_name, $options = array()) {
		$defaults = array('selected' => Date('m'));
		$options = array_merge($defaults,$options);

		$data_list = array();
		foreach (t('months', array('from' => 'date')) as $key => $value) {
			$selected = '';
			$data_list[$key] = $value;
		}

		$html = $this->select($field_name, $data_list, $options);
		return $html;
	}


	/**
	 * Get html select with all days
	 *
	 * @param string $field_name
	 * @param array $options @see HtmlHelper::select
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

	/**
	 * Get html select for date (day, month, year)
	 *
	 * @param field_name
	 * @param options @see HtmlHelper::select
	 * @return string
	 */
	public function show_select_date($field_name, $options = array()) {
		$options = array_merge(array('start_year'=>Date('Y')-120,'end_year'=>Date('Y')),$options);

		$html = $this->select_date_days($field_name.'[day]',$options);
		$html .= $this->select_date_months($field_name.'[month]',$options);
		$html .= $this->select_date_years($field_name.'[year]',$options['start_year'],$options['end_year'],$options);

		return $html;
	}

	/**
	 * Create html attributes
	 *
	 * @param $attributes
	 * @return string
	 */
	public function create_attributes($attributes) {
		$attributes_list = array('rel','class','title','id','alt','value','name','data-method','data-confirm','enctype','disabled','checked');
		$data = "";

		foreach ($attributes as $key => $value) {
			if (in_array($key,$attributes_list)) {
				$data .= '' . $key . '="' . $value . '" ';
			}
		}

		return $data;
	}

}
?>