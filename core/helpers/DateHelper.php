<?
/**
 * Drumon Framework: Build fast web applications
 * Copyright (C) 2010 Sook - Desenvolvendo inovações (http://www.sook.com.br)
 * Licensed under GNU General Public License.
 *
 * Helper para trabalhar com data.
 *
 * @author Sook contato@sook.com.br
 * @package helpers
 */
class DateHelper extends Helper {

	/**
	 * Retorna data em forma escrita de acordo com a i18n.
	 *
	 * Exemplo: 29 de abril de 2009.
	 *
	 * @access public
	 * @param string $date - Data a ser processada.
	 * @return string - Data no formato padrão da i18n.
	 */
	function inWords($date) {
		if($this->i18n['lang'] === 'pt-br'){
			setlocale(LC_ALL, 'portuguese', 'pt_BR', 'pt_br', 'ptb_BRA');
		}
		return strftime($this->i18n['date']['inWords'],strtotime($date));
	}

	/**
	 * Obtém o hora e os minutos de uma data.
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

	/**
	 * Retorna a data de acordo com o formato padrão da i18n.
	 *
	 * @access public
	 * @param string $date - Data a ser processada.
	 * @return string - Data de acordo com o padrão da i18n.
	 */
	function show($date = null) {
		$format = str_replace("%", "", $this->i18n['date']['default']);
		if(empty($date)) return date ($format);
		return date ($format, strtotime($date));
	}
}
?>
