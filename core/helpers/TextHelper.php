<?
/**
 * Helper para trabalhar com texto.
 *
 * @author Sook contato@sook.com.br e terceiros.
 * @package helpers
 */
class TextHelper extends SKHelper {
	
	/**
	 * Converte um texto para o formato de slug, retirando os acentos e espaços.
	 *
	 * @access public
	 * @param text $string
	 * @param string $space - Caractere usado no lugar do espaço (default: -).
	 * @return string
	 */
	function toSlug($text, $space = "-") {
		$text = trim($text);

		$search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u");
		$replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u");
		$text = str_replace($search, $replace, $text);


		if (function_exists('iconv')) {
			$text = @iconv('UTF-8', 'ASCII//TRANSLIT', $text);
		}

		$text = preg_replace("/[^a-zA-Z0-9 -]/", "", $text);
		$text = strtolower($text);
		$text = str_replace(" ", $space, $text);
		return $text;
	}

	/**
	 * Adiciona um link no final de um post para leitura completa do post.
	 *
	 * @access public
	 * @param string $post
	 * @param string $read_more
	 * @param string $url
	 * @return array
	 */
	function blog($post, $read_more = 'Read more...',$url) {
		$text = explode('<!--more-->',$post['content']);

		if(count($text) === 1) return $text[0];
		$text[0] .= '<a class="readmore" title="'.$read_more.'" href="'.$url.'">'.$read_more.'</a>';
		return $text[0];
	}

	/**
	 * Procura Tags de usuário twitter e hasttags para consulta no twitter e adiciona link na tag encontrada.
	 *
	 * @access public
	 * @param string $text
	 * @return string
	 */
	function twitterify($ret) {
	  $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $ret);
	  $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $ret);
	  $ret = preg_replace("/@(\w+)/", "<a title= \"Twitter Profile\" href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
	  $ret = preg_replace("/#(\w+)/", "<a title=\"Twitter Search\" href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
		return $ret;
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
	function highlight($text, $phrase, $options = array()) {
		if (empty($phrase)) {
			return $text;
		}

		$default = array(
			'format' => '<span class="highlight">\1</span>',
			'html' => false
		);
		$options = array_merge($default, $options);
		extract($options);

		if (is_array($phrase)) {
			$replace = array();
			$with = array();

			foreach ($phrase as $key => $segment) {
				$segment = "($segment)";
				if ($html) {
					$segment = "(?![^<]+>)$segment(?![^<]+>)";
				}

				$with[] = (is_array($format)) ? $format[$key] : $format;
				$replace[] = "|$segment|iu";
			}

			return preg_replace($replace, $with, $text);
		} else {
			$phrase = "($phrase)";
			if ($html) {
				$phrase = "(?![^<]+>)$phrase(?![^<]+>)";
			}

			return preg_replace("|$phrase|iu", $format, $text);
		}
	}


	/**
	* Tira de determinado texto de todos os links.
	*
	* @param string $text
	* @return string
	* @access public
	*/
	function stripLinks($text) {
		return preg_replace('|<a\s+[^>]+>|im', '', preg_replace('|<\/a>|im', '', $text));
	}

	/**
	 * Adiciona links (<a href =....) a um determinado texto, encontrando texto que começa com
	 * strings como http://.
	 *
	 * @param string $text

	 */
	function linkfy($text) {
		return preg_replace("#http://([A-z0-9./-]+)#", '<a href="$1">$0</a>', $text);
	}



	/**
	 * Truca Strings (Textos)
	 * Corta uma string com o comprimento de $length e substitui o último caracteres
	 * com o fim se o texto for maior que o comprimento.
	 *
	 * ### Opções:
	 *
	 * - `ending` Será utilizado como Ending e anexado à string cortada.
	 * - `exact` Se falço, $text não será cortado do texto completo.
	 * - `html` Se verdadeiro , as tags HTML serão tratadas corretamente.
	 *
	 * @param string  $text
	 * @param integer $length
	 * @param array $options
	 * @return string
	 * @access public
	 */
	function truncate($text, $length = 100, $options = array()) {
		$default = array(
			'ending' => '...', 'exact' => true, 'html' => false
		);
		$options = array_merge($default, $options);
		extract($options);

		if ($html) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}
			$totalLength = mb_strlen(strip_tags($ending));
			$openTags = array();
			$truncate = '';

			preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
			foreach ($tags as $tag) {
				if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
					if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
						array_unshift($openTags, $tag[2]);
					} else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
						$pos = array_search($closeTag[1], $openTags);
						if ($pos !== false) {
							array_splice($openTags, $pos, 1);
						}
					}
				}
				$truncate .= $tag[1];

				$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
				if ($contentLength + $totalLength > $length) {
					$left = $length - $totalLength;
					$entitiesLength = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1] + 1 - $entitiesLength <= $left) {
								$left--;
								$entitiesLength += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}

					$truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
					break;
				} else {
					$truncate .= $tag[3];
					$totalLength += $contentLength;
				}
				if ($totalLength >= $length) {
					break;
				}
			}
		} else {
			if (mb_strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = mb_substr($text, 0, $length - mb_strlen($ending));
			}
		}
		if (!$exact) {
			$spacepos = mb_strrpos($truncate, ' ');
			if (isset($spacepos)) {
				if ($html) {
					$bits = mb_substr($truncate, $spacepos);
					preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
					if (!empty($droppedTags)) {
						foreach ($droppedTags as $closingTag) {
							if (!in_array($closingTag[1], $openTags)) {
								array_unshift($openTags, $closingTag[1]);
							}
						}
					}
				}
				$truncate = mb_substr($truncate, 0, $spacepos);
			}
		}
		$truncate .= $ending;

		if ($html) {
			foreach ($openTags as $tag) {
				$truncate .= '</'.$tag.'>';
			}
		}

		return $truncate;
	}

	/**
	 * Extrai um trecho do texto em torno da frase com um número de caracteres de cada lado determinado pelo raio.
	 *
	 * @param string $text
	 * @param string $phrase
	 * @param integer $radius
	 * @param string $ending
	 * @return string
	 * @access public
	 */
	function excerpt($text, $phrase, $radius = 100, $ending = '...') {
		if (empty($text) or empty($phrase)) {
			return $this->truncate($text, $radius * 2, array('ending' => $ending));
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
	 * Traduz o Texto usando a i18n.
	 *
	 * @param string $key - Chave da palavra no arquivo de i18n.
	 * @param boolean $ucfirst
	 *
	 * @return string - O texto traduzido.
	 * @access public
	 */
	function locale($key, $ucfirst = false) {
		if($ucfirst) return ucfirst($this->i18n[$key]);
		return $this->i18n[$key];
	}
}
?>
