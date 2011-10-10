<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Text manipulation.
 *
 * @package drumon_core
 * @subpackage	drumon_core.helpers
 */
class TextHelper extends Helper {

	/**
	 * Converts a text format for the slug, removing accents and spaces
	 *
	 * @param string $text Text to be formatted
	 * @param string $space Character used instead of special characters
	 * @return string
	 */
	public function to_slug($text, $space = "-") {
		return App::to_slug($text, $space);
	}

	/**
	 * Search usernames and consultation for hashtags on twitter and add the link tags found
	 *
	 * @param string $text Text to be formatted
	 * @return string
	 */
	public function twitterify($text) {
		$text = preg_replace("/@(\w+)/", "<a title= \"Twitter Profile\" href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $text);
		$text = preg_replace("/#(\w+)/", "<a title=\"Twitter Search\" href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $text);
		return $text;
	}

	/**
	 * Adds links (<a href =....) a given text, finding text that begins with strings such as http://
	 *
	 * @param string $text Text to be formatted
	 * @return string
	 */
	public function linkfy($text) {
		$text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);
		$text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
		return $text;
	}

	/**
	 * Highlight words in text
	 *
	 * @param string $text Text to search the phrase in
	 * @param string $phrase The phrase that will be searched
	 * @param array $options An array of html attributes and options (optional)
	 * @return string
	 * ### Options:
	 *
	 * - `format` text replace format (<span class="highlight">\1</span>)
	 */
	public function highlight($text, $phrase, $options = array()) {

		if (empty($phrase)) {
			return $text;
		}

		$default = array(
			'format' => '<span class="highlight">\1</span>'
		);

		$options = array_merge($default, $options);

		if (is_array($phrase)) {
			$replace = array();
			$with = array();

			foreach ($phrase as $key => $segment) {
				$segment = "($segment)";

				$with[] = (is_array($options['format'])) ? $options['format'][$key] : $options['format'];
				$replace[] = "|$segment|iu";
			}

			return preg_replace($replace, $with, $text);
		} else {
			$phrase = "($phrase)";

			return preg_replace("|$phrase|iu", $options['format'], $text);
		}
	}


	/**
	* Remove links from text
	*
	* @param string $text
	* @return string
	*/
	public function strip_links($text) {
		return preg_replace('|<a\s+[^>]+>|im', '', preg_replace('|<\/a>|im', '', $text));
	}

	/**
	 * Truncate text
	 *
	 * @param string $text
	 * @param int $max character number
	 * @param array $options (end, exact)
	 * @return string
	 *
	 */
	public function truncate($text, $max, $options = array()) {
		$options = array_merge(array('end' => '...', 'exact' => true), $options);
		if (strlen($text) > $max) {
			$text = substr($text, 0, $max);
			if (!$options['exact']) {
				$text = substr($text,0,strrpos($text, " "));
			}
			$text .= $options['end'];
		}
		return $text;
	}

	/**
	 * Text excerpt
	 *
	 * @param string $text
	 * @param string $search_text
	 * @param integer $radius
	 * @param string $ending
	 * @return string
	 */
	public function excerpt($text, $search_text, $radius = 100, $ending = '...') {

		if (empty($text) or empty($search_text)) {
			return $this->truncate($text, $radius * 2, array('end' => $ending));
		}

		$search_text_lenght = mb_strlen($search_text);
		if ($radius < $search_text_lenght) {
			$radius = $search_text_lenght;
		}

		$pos = mb_strpos(mb_strtolower($text), mb_strtolower($search_text));

		$startPos = 0;
		if ($pos > $radius) {
			$startPos = $pos - $radius;
		}

		$text_lenght = mb_strlen($text);

		$endPos = $pos + $search_text_lenght + $radius;
		if ($endPos >= $text_lenght) {
			$endPos = $text_lenght;
		}

		$excerpt = mb_substr($text, $startPos, $endPos - $startPos);
		if ($startPos != 0) {
			$excerpt = substr_replace($excerpt, $ending, 0, $search_text_lenght);
		}

		if ($endPos != $text_lenght) {
			$excerpt = substr_replace($excerpt, $ending, -$search_text_lenght);
		}

		return $excerpt;
	}


	/**
	 * Translate text using i18n system
	 *
	 * @param string $key
	 * @return string
	 */
	public function translate($key, $options = array()) {
		return t($key, $options);
	}
}
?>