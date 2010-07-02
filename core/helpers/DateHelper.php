<?
/**
 * Helper's para trabalhar com data
 * @author Sook contato@sook.com.br
 * @package default
 */
class DateHelper extends SKHelper {
	/**
	 * Retorna data em forma escrita
	 * Exemplo: 29 de month de 2009
	 * @access public
	 * @param Date $date
	 * @return String
	 */
	function inWordsOld ($date) {
		$date = explode(' ', $date);
		list ($year, $month, $day) = preg_split('/[\/.-]/', $date[0]);
		$inWords = $this->i18n['date']['inWords'];
		$inWords = str_replace("%d", $day, $inWords);
		$inWords = str_replace("%B", $this->i18n['date']['months'][$month-1], $inWords);
		$inWords = str_replace("%Y", $year, $inWords);
		return $inWords;
	}

	/**
	 * Retorna a data em formato Português Brasileiro
	 * @access public
	 * @param Date $date
	 * @return Datetime
	 */
	function inWords($date) {
		if($this->i18n['lang'] === 'pt-br'){
			setlocale(LC_ALL, 'portuguese', 'pt_BR', 'pt_br', 'ptb_BRA');
		}
		return strftime($this->i18n['date']['inWords'],strtotime($date));
	}

	/**
	 * Extrae o tempo de uma data
	 * @access public
	 * @param Datetime $date
	 * @return Datetime
	 */
	function time ($date) {
		$date = explode(' ', $date);
		list ($hour, $minutes, $second) = explode(':', $date[1]);
		return $hour.":".$minutes;
	}

	/**
	 * Mostra data caso ela seja nula
	 * @access public
	 * @param Datetime $date
	 * @return Datetime
	 */
	function show($date = null) {
		$format = str_replace("%", "", $this->i18n['date']['default']);
		if(empty($date)) return date ($format);
		return date ($format, strtotime($date));
	}
}
?>