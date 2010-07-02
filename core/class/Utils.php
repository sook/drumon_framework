<?php
/**
 * Classe de Utilitários
 *
 * @package default
 * @author Sook contato@sook.com.br
 */

class Utils {
	/**
	 * Checa Email
	 * @access public
	 * @param string $eMailAddress
	 * @return boolean
	 */
	public static function checkEmail($eMailAddress) {
		if (eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$", $eMailAddress, $check)) {
			return true;
		}
		return false;
	}

	/**
	 * Envia Email
	 * @access public
	 * @param string $nome
	 * @param string $to
	 * @param string $subject
	 * @param string $content
	 * @return boolean
	 */
	public static function mail($name,$to,$subject,$content) {
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		if (mail($to,$subject,$content,$headers)) {
			return true;
		} else {
			return false;
		}
	}
}