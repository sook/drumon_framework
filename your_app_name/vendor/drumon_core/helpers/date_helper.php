<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Support for handling dates.
 *
 * @package helpers
 */
class DateHelper extends Helper {
	
	/**
	 * Retorna a data passando o formato do locale.
	 *
	 * Veja todas as opções de formatação em {@link http://ch2.php.net/manual/pt_BR/function.strftime.php http://ch2.php.net/manual/pt_BR/function.strftime.php}.
	 *
	 * Exemplo: 29 de abril de 2009.
	 *
	 * @param string $date
	 * @param string $format Format name from locale (optional)
	 * @return string
	 */
	public function show($date, $format = 'default') {
		return strftime(t('formats.' . $format, array('from' => 'date')), strtotime($date));
	}
	
	/**
	 * Get current date formated
	 *
	 * @param string $format (optional)
	 * @return string
	 */
	public function now($format = 'default') {
			return strftime(t('formats.' . $format, array('from' => 'date')));
	}

	/**
	 * Get time from Date
	 *
	 * @access public
	 * @param string $date
	 * @return string time in this format 23:59
	 */
	public function time ($date) {
		$date = explode(' ', $date);
		list ($hour, $minutes, $second) = explode(':', $date[1]);
		return $hour . ":" . $minutes;
	}
	
}
?>