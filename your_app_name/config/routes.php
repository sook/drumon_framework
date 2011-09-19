<?php
	
	//  Basic route
	// 	$route['get']['/blog'] = array('Blog::index');

	// 	Dynamic route
	// 	$route['get']['/tag/{tag}'] = array('Blog::tags');
	
	// 	Dynamic route with regex
	// 	$route['get']['/tag/{tag}'] = array('Blog::tags','{tag}'=>'[a-zA-Z0-9_]');

	//	Named routes
	//  $route['get']['/blog'] = array('Blog::index','as'=>'blog'); //in view => $url->to_blog();
	//  $route['get']['/tag/{tag}'] = array('Blog::tags','as'=>'tag'); //in view => $url->to_tag('your-tag-name');

	// 	Redirect routes
	 	$route['*']['/google'] = array('redirect'=>'http://google.com');
	
	 	$route['*']['/twitter'] = array('redirect' => 'http://twitter.com', 302);
	
	
	//	404 error route
	//	$route['404'] = array('Error::error_404');
	
	// Route for application root
	$route['get']['/'] = array('Pages::index');
	
?>