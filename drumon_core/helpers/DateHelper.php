<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Helper para trabalhar com data.
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class DateHelper extends Helper {

	/**
	 * Retorna a data passando o formato do i18n.
	 *
	 * Exemplo: 29 de abril de 2009.
	 *
	 * @access public
	 * @param string $date - Data a ser processada.
	 * @param string $format - Formato definido no i18n.
	 * @return string - Data no formato padrão da i18n.
	 */
	function show($date, $format = 'default') {
		return strftime($this->i18n['date'][$format],strtotime($date));
	}
	
	/**
	 * Retorna a data atual passando o formato do i18n
	 *
	 * @param string $format 
	 * @return void
	 * @author Danillo César de Oliveira Melo
	 */
	function now($format = 'default') {
			return strftime($this->i18n['date'][$format]);
	}

	/**
	 * Obtém a hora e os minutos de uma data.
	 *
	 * @access public
	 * @param string $date - Data a ser processada.
	 * @return string - Hora no formato 23:59.
	 */
	function time ($date) {
		$date = explode(' ', $date);
		list ($hour, $minutes, $second) = explode(':', $date[1]);
		return $hour.":".$minutes;
	}
	
}
?>