<?
/**
 * Helpers do framework
 *
 * @package class
 * @abstract
 * @author Sook contato@sook.com.br
 */
abstract class SKHelper {
	
	/** 
	 * Referência da variável com os dados de internacionalização
	 *
	 * @access protected
	 * @name $i18n
	 */
	protected $i18n;

	public $uses = array();
	/**
	 * Construtora da Classe
	 * @access public
	 * @param String $i18n
	 * @return void
	 */
	public function __construct($i18n){
		$this->i18n = $i18n;
	}

	/**
	 * Sprintf para utilização com array
	 * @access public
	 * @param String $str
	 * @param Array $vars
	 ) @param String $char
	 * @return String
	 */
	public function sprintf2($str='', $vars=array(), $char='%') {
	    if (!$str) return '';
	    if (count($vars) > 0) {
	        foreach ($vars as $k => $v) {
	            $str = str_replace($char . $k, $v, $str);
	        }
	    }
	    return $str;
	}


}
?>
