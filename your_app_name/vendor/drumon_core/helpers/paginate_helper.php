<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe de paginação que será utilizada para listagem dos regitros do Drumon CMS.
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class PaginateHelper extends Helper {
	
	var $uses = array('Text');


	/**
	 * Verifica a existência de paginação.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @return bool - True se a ultima página for maior que 1, False se não.
	 */
	public function has_page($page) {
		return $page->has_page();
	}

	/**
	 * Verifica a existência de próxima página.
	 *
	 * @access public
	 * @param object $page
	 * @return bool
	 */
	public function has_next($page) {
		return $page->has_nextPage();
	}

	/**
	 * Verifica a existência de página anterior.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @return bool - True se a pagina atual for maior que 1 , false se não.
	 */
	public function has_prev($page) {
		return $page->has_prevPage();
	}

	/**
	 * Retorna o número da página atual.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @return int - Número da página atual.
	 */
	public function current($page) {
		return $page->current_page;
	}

	/**
	 * Retorna a URL da próxima página.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @return string - 
	 */
	public function next_url($page, $url = "") {
		return $this->get_formated_url($page->getNextPage(), $url);
	}

	/**
	 * Retorna a URL da página anterior.
	 *
	 * @access Public
	 * @param object $page - Referência a uma instância de Page.
	 * @param string $url - Url para formatação do link.
	 * @return string - Url formatada para visualização da página anterior.
	 */
	public function previous_url($page, $url = "") {
		return $this->get_formated_url($page->getPrevPage(), $url);
	}

	/**
	 * Retorna a URL da ultima página.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @param string $url - Url para formatação do link.
	 * @return string - Url formatada para visualização da ultima página.
	 */
	public function last_page_url($page, $url = "") {
		return $this->get_formated_url($page->getLastPage(), $url);
	}

	/**
	 * Retorna a paginação em html.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @param array $options - Lista de opções para construção da div de paginação.
	 * @return string - Código html da paginação.
	 */
	public function show($page, $options = array()) {
		if(!$this->has_page($page)) return '';

		$defaults = array('type'=>'full','class'=>'paginate','url'=>'','range'=>10);
		$options = array_merge($defaults, $options);

		$types = array(
			'full' => array('_prev','_pages','_next'),
			'simple' => array('_prev','_next'),
			'next' => array('_next'),
			'prev' => array('_prev')
		);

		$html = '<div class="'.$options['class'].'">';
		foreach ($types[$options['type']] as $value) {
			$html .= $this->$value($page,$options);
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Retorna a paginação anterior em html.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @param array $options - Lista de opções para construção da div de paginação.
	 * @return string - Código html da paginação.
	 */
	public function _prev($page,$options) {
		$defaults = array('show'=>true);
		$options = array_merge($defaults, $options);
		$text = isset($options['text']) ? $options['text'] : $this->text->t('pagination.prev_page');

		if($this->has_prev($page)) {
			return '<a class="prev_page" href="'.$this->previous_url($page,$options['url']).'" title="'.$text.'">'.$text.'</a>';
		} else {
			if($options['show']){
				$html = '<span class="disabled prev_page">'.$text.'</span>';
			}else{
				$html = '';
			}
			return $html;
		}
	}

	/**
	 * Retorna a paginação em html.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @param array $options - Lista de opções para construção da div de paginação.
	 * @return string - Código html da paginação.
	 */
	public function _pages($page,$options) {
		$range = $options['range'];
		$range--;
		$pages_list = array();

		$offset_prev =  ($this->current($page)-$range);
		if($offset_prev < 1) $offset_prev = 1;
		for ($i=$offset_prev; $i < $this->current($page); $i++) {
			$pages_list[] = $i;
		}

		$pages_list[] = $this->current($page);

		$offset_next =  ($this->current($page)+$range);

		if($offset_next > $page->total_pages) $offset_next = $page->total_pages;
		for ($i=$this->current($page); $i < $offset_next; $i++) {
			$pages_list[] = $i+1;
		}


		$html = "";
		foreach ($pages_list as $p) {
			# code...
			if ($this->current($page) == $p) {
				$html .= '<span class="current number">'.$p.'</span>';
			} else {
				$html .= '<a class="number" href="'.$this->get_formated_url($p,$options['url']).'">'.$p.'</a>';
			}
		}

		return $html;
	}

	/**
	 * Retorna a próxima paginação em relação a atual.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @param array $options - Lista de opções para construção da div de paginação.
	 * @return string - Código html da paginação.
	 */
	public function _next($page,$options) {
		$defaults = array('show'=>true);
		$options = array_merge($defaults, $options);
		$text = isset($options['text']) ? $options['text'] : $this->text->t('pagination.next_page');

		if($this->has_next($page)) {
			return '<a class="next_page" href="'.$this->next_url($page,$options['url']).'" title="'.$text.'">'.$text.'</a>';
		} else {
			if($options['show']){
				$html = '<span class="disabled next_page">'.$text.'</span>';
			}else{
				$html = '';
			}
			return $html;
		}
	}

	/**
	 * Retorna as informações de geração de paginação.
	 *
	 * @access public
	 * @param object $page - Referência a uma instância de Page.
	 * @return string - Informações de geração de paginação.
	 */
	public function info($page) {
		$current_page = $page->current_page;
		$per_page = $page->per_page;
		$records = count($page->results);

		$offset = (($current_page-1) * $per_page);
		$init = $offset + 1;
		$end = $offset + $records;
		
		$translated = $this->text->t('pagination.page_info');
		
		if ($page->total_pages < 2) {
			
			switch ($page->total_records) {
			case 0:
	        echo $translated['0'];
	        break;
	    case 1:
	        echo $translated['1'];
	        break;
	    default;
	        echo $this->sprintf2($translated['all'],array('value'=>$page->total_records));
	        break;
			}
		} else {
			echo $this->sprintf2($translated['range'],array('from'=>$init,'to'=>$end,'all'=>$page->total_records));
		}
	}

	/**
	 * Retorna a URL formatada para paginação.
	 *
	 * @access public
	 * @param integer $pageNumber - Número da página.
	 * @param string $url - Url para concatenação.
	 * @return string - Url completa com o link de paginação.
	 */
	public function get_formated_url($pageNumber, $url = "") {
		$params = "";
		$currentUrl = $this->get_url();
		if (strpos($currentUrl,'?') != false) {
			$params = substr($currentUrl, strpos($currentUrl,'?'), strlen($currentUrl));
		}
		return APP_DOMAIN.$url."/".$pageNumber.'/'.$params;
	}

	/**
	 * Retorna o fullpath da pagina com as variáveis get.
	 *
	 * @access private
	 * @return string - Url completa com variáveis get.
	 */
	private function get_url(){
		$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = $this->strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
	}

	/**
	 * Remove um texto de outro texto.
	 *
	 * @access private
	 * @param string $s1 - Texto que será extraído do outro.
	 * @param string $s2 - Texto alvo de extração.
	 * @return string - Texto com com extração da string determinada.
	 */
	private function strleft($s1, $s2) {
		return substr($s1, 0, strpos($s1, $s2));
	}
}
?>
