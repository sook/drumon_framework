<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 *
 * Classe de Utilitários.
 *
 * @package class
 * @author Sook contato@sook.com.br
 */

class Utils {
	/**
	 * Checa Email.
	 *
	 * @access public
	 * @param string $eMailAddress - Email a ser verificado.
	 * @return boolean - True se o email é válido / False se não.
	 */
	public static function checkEmail($eMailAddress) {
		if (eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$", $eMailAddress, $check)) {
			return true;
		}
		return false;
	}

	/**
	 * Envia Email utilizando a função mail.
	 *
	 * @access public
	 * @param string $name - Nome de rementente do email.
	 * @param string $to - Email destinatário.
	 * @param string $subject - Assunto.
	 * @param string $content - Conteúdo do email.
	 * @return boolean - True se o envio foi bem sucessido / False se não.
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
?>
