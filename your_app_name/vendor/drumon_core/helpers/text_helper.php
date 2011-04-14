<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Helper para trabalhar com texto.
 *
 * @author Sook contato@sook.com.br.
 * @package helpers
 */
class TextHelper extends Helper {
	
	public $translations = array();
	
	/**
	 * Converte um texto para o formato de slug, retirando os acentos e espaços.
	 *
	 * @access public
	 * @param string $text - Texto a ser formatado.
	 * @param string $space - Caractere usado no lugar do espaço (default: -).
	 * @return string -  Texto formatado.
	 */
	public function to_slug($text, $space = "-") {
		return App::to_slug($text, $space);
	}

	/**
	 * Procura Tags de usuário twitter e hasttags para consulta no twitter e adiciona link na tag encontrada.
	 *
	 * @access public
	 * @param string $text - Texto a ser verificado.
	 * @return string - Texto Modificado pelo método.
	 */
	public function twitterify($text) {
	  $text = preg_replace("/@(\w+)/", "<a title= \"Twitter Profile\" href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $text);
	  $text = preg_replace("/#(\w+)/", "<a title=\"Twitter Search\" href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $text);
		return $text;
	}
	
	/**
	 * Adiciona links (<a href =....) a um determinado texto, encontrando texto que começa com
	 * strings como http://.
	 *
	 * @param string $text - Texto a ser analisado.
	 * @return string - Texto com inclusão de links.

	 */
	public function linkfy($text) {
		$text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);
	  $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
	//	preg_replace("#http://([A-z0-9./-]+)#", '<a href="$1">$0</a>', $text);
		return $text;
	}

	/**
	 * Destaca uma determinada frase em um texto.
	 *
	 * @access public
	 * @param string $text - Text to search the phrase in.
	 * @param string $phrase - The phrase that will be searched.
	 * @param array $options - An array of html attributes and options.
	 * @return string - The highlighted text.
	 * ### Options:
	 *
	 * - `format` O pedaço de html com que a frase será destaque
	 * - `html` Se for verdade, irá ignorar todas as tags HTML, garantindo que apenas o texto correto é destaque.
	 */
	public function highlight($text, $phrase, $options = array()) {
		if (empty($phrase)) {
			return $text;
		}

		$default = array(
			'format' => '<span class="highlight">\1</span>'
		);
		
		$options = array_merge($default, $options);
		extract($options);

		if (is_array($phrase)) {
			$replace = array();
			$with = array();

			foreach ($phrase as $key => $segment) {
				$segment = "($segment)";

				$with[] = (is_array($format)) ? $format[$key] : $format;
				$replace[] = "|$segment|iu";
			}

			return preg_replace($replace, $with, $text);
		} else {
			$phrase = "($phrase)";

			return preg_replace("|$phrase|iu", $format, $text);
		}
	}


	/**
	* Remove os links de um texto.
	*
	* @param string $text - Texto a ser verificado.
	* @return string - Texto sem links.
	* @access public
	*/
	public function strip_links($text) {
		return preg_replace('|<a\s+[^>]+>|im', '', preg_replace('|<\/a>|im', '', $text));
	}

	/**
	 * Corta uma string com o comprimento de $max e substitui o último caracteres
	 * com o fim se o texto for maior que o comprimento.
	 *
	 * @param string $text 
	 * @param int $max 
	 * @param array $options 
	 * @return string
	 * 
	 */
	public function truncate($text, $max, $options = array()) {
		$options = array_merge(array('end'=>'...', 'exact' => true), $options);
		if (strlen($text) > $max) {
			$text = substr($text, 0, $max);
			if (!$options['exact']) {
				$text = substr($text,0,strrpos($text," "));
			}
			$text .= $options['end'];
		}
		return $text;
	}
	
	/**
	 * Extrai um trecho do texto em torno da frase com um número de caracteres de cada lado determinado pelo raio.
	 *
	 * @param string $text - Texto a ser analisado.
	 * @param string $phrase - Frase para extração.
	 * @param integer $radius - Tamanho padrão de caracteres excedentes.
	 * @param string $ending - Texto padrão para fechamento do texto analisado.
	 * @return string - Texto com as modificações.
	 * @access public
	 */
	public function excerpt($text, $phrase, $radius = 100, $ending = '...') {
		if (empty($text) or empty($phrase)) {
			return $this->truncate($text, $radius * 2, array('end'=>$ending));
		}

		$phraseLen = mb_strlen($phrase);
		if ($radius < $phraseLen) {
			$radius = $phraseLen;
		}

		$pos = mb_strpos(mb_strtolower($text), mb_strtolower($phrase));

		$startPos = 0;
		if ($pos > $radius) {
			$startPos = $pos - $radius;
		}

		$textLen = mb_strlen($text);

		$endPos = $pos + $phraseLen + $radius;
		if ($endPos >= $textLen) {
			$endPos = $textLen;
		}

		$excerpt = mb_substr($text, $startPos, $endPos - $startPos);
		if ($startPos != 0) {
			$excerpt = substr_replace($excerpt, $ending, 0, $phraseLen);
		}

		if ($endPos != $textLen) {
			$excerpt = substr_replace($excerpt, $ending, -$phraseLen);
		}

		return $excerpt;
	}


	/**
	 * Traduz o Texto usando a variável de internacionalização locale.
	 *
	 * @param string $key - Chave da palavra no arquivo de locale.
	 * @return string - O texto traduzido.
	 * @access public
	 */
	public function translate($text) {
		return t($text);
	}
}
?>
