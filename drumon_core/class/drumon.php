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
	 * Protege contra ataques do tipo CSRF.
	 *
	 * @param object $request 
	 * 
	 */
	public static function fire_csrf_protection($request) {
		$token  = dechex(mt_rand());
		$hash   = sha1(APP_SECRET.APP_DOMAIN.'-'.$token);
		$signed = $token.'-'.$hash;

		// Token criado para usar nos formulários.
		define('REQUEST_TOKEN',$signed);

		if ($request->method != 'get') {
			$unauthorized = true;

			if (!empty($request->params['_token'])) {
				$parts = explode('-',$request->params['_token']);

				if (count($parts) == 2) {
			    list($token, $hash) = $parts;
			    if ($hash == sha1(APP_SECRET.APP_DOMAIN.'-'.$token)) {
						$unauthorized = false;
					}
				}
			}

			// Bloqueia a requisção não autorizada.
			if($unauthorized) {
				header("HTTP/1.0 401 Unauthorized");
				include(ROOT.'/public/'.$request->routes['401']);
				die();
			}
		}
	}
	
	/**
	 * Transforma palavrasEmCamelCase para palavras_em_underscore
	 *
	 * @param string $camelCasedWord 
	 * @return string
	 */
	public static function to_underscore($camelCasedWord) {
		$result = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
		return $result;
	}
	
	
	/**
	 * Transforma palavras_em_underscore em PalavrasEmCamelCase
	 *
	 * @param string $lowerCaseAndUnderscoredWord 
	 * @return string
	 */
	public static function to_camelcase($lowerCaseAndUnderscoredWord) {
		$lowerCaseAndUnderscoredWord = ucwords(str_replace('_', ' ', $lowerCaseAndUnderscoredWord));
		$result = str_replace(' ', '', $lowerCaseAndUnderscoredWord);
		return $result;
	}
}


?>