<?php
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 */

/**
 * Support for handling dates.
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class DateHelper extends Helper {
	
	
	var $uses = array('Text');
	
	/**
	 * Retorna a data passando o formato do locale.
	 * Veja todas as opções de formatação em {@link http://ch2.php.net/manual/pt_BR/function.strftime.php http://ch2.php.net/manual/pt_BR/function.strftime.php}.
	 * Exemplo: 29 de abril de 2009.
	 *
	 * @access public
	 * @param string $date - Data a ser processada.
	 * @param string $format - Formato definido no locale.
	 * @return string - Data no formato padrão da locale.
	 */
	public function show($date, $format = 'default') {
		return strftime(t('date.'.$format),strtotime($date));
	}
	
	/**
	 * Retorna a data atual passando o formato do locale
	 *
	 * @param string $format 
	 * @return void
	 * @author Danillo César de Oliveira Melo
	 */
	public function now($format = 'default') {
			return strftime(t('date.'.$format));
	}

	/**
	 * Obtém a hora e os minutos de uma data.
	 *
	 * @access public
	 * @param string $date - Data a ser processada.
	 * @return string - Hora no formato 23:59.
	 */
	public function time ($date) {
		$date = explode(' ', $date);
		list ($hour, $minutes, $second) = explode(':', $date[1]);
		return $hour.":".$minutes;
	}
	
}
?>