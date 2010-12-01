<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Classe com métodos essências para funcionamento do Drumon Framework
 *
 * @package class
 */
class Drumon {
	
	/**
	 * Transforma palavrasEmCamelCase para palavras_em_underscore
	 *
	 * @param string $camelCasedWord 
	 * @return string
	 */
	function to_underscore($camelCasedWord) {
		$result = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
		return $result;
	}
	
	
	/**
	 * Transforma palavras_em_underscore em PalavrasEmCamelCase
	 *
	 * @param string $lowerCaseAndUnderscoredWord 
	 * @return string
	 */
	function to_camelcase($lowerCaseAndUnderscoredWord) {
		$lowerCaseAndUnderscoredWord = ucwords(str_replace('_', ' ', $lowerCaseAndUnderscoredWord));
		$result = str_replace(' ', '', $lowerCaseAndUnderscoredWord);
		return $result;
	}
}


?>