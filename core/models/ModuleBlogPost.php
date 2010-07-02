<?
/**
 * Módulo para post para blog
 *
 * @author Sook contato@sook.com.br
 * @package models
 */
class ModuleBlogPost extends AppModel {

	public $table = "blog_posts";
	protected $uses = array('trash','status');

	public function __construct() {
		parent::__construct();
		$this->imports('Page');
		$this->imports('Selector');
		$this->imports('Tag');
		$this->imports('Comment');
	}
	
}
?>
