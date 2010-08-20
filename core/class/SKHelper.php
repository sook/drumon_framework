<?
/**
 * Helpers do framework.
 *
 * @package class
 * @abstract
 * @author Sook contato@sook.com.br
 */
abstract class SKHelper {
	
	/** 
	 * Referência da variável com os dados de internacionalização.
	 *
	 * @access protected
	 * @var string
	 */
	protected $i18n;
	
	/** 
	 * Lista de outros helpers que vão ser utilizados no helper atual.
	 *
	 * @access public 
	 * @var array
	 */
	public $uses = array();
	
	/**
	 * Construtora da Classe
	 *
	 * @access public
	 * @param string $i18n Referência da variável com os dados de internacionalização.
	 * @return void
	 */
	public function __construct($i18n){
		$this->i18n = $i18n;
	}

	/**
	 * Substitui os parâmetros de uma string pelos valores de um array de hash.
	 *
	 * @access public
	 * @param string $str
	 * @param array $vars
	 * @param string $char
	 * @return string
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
