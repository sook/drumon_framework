<?php


/**
* mockup
*/
class Validation {
	
	
}


/**
* 
*/
class Comment extends Model {
	$validations = array(
		'title' => array(
			'required' => array(),
			'format' => array('with'=>'/[a-z]/')
		),
		
		'content' => array(
		
		)
	);
	
	public function validations() {
		$this->required('title',array('message' => 'Requerido'));
		$this->required('content');
		$this->length('title',array('min' => 2, 'max' => 10, 'in' => '2,10', 'equal' => 6));
		$this->format('title',array('with'=>'/[a-z]/'));
		
		
		
	}
	
}


$comment = new Comment($this->params['comment']);

if ($comment->valid) {

}

?>