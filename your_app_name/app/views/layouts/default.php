<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="pt-br">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="content-language" content="pt-br">
		<meta charset="UTF-8">	
		<meta name="Keywords" content="">
		<meta name="Description" content="">
		<meta name="Author" content="">
		<meta name="csrf-token" content="<? echo REQUEST_TOKEN; ?>">
		
		<title>Drumon Framework - <? echo $html->block('title'); ?></title>
		<?php echo $html->css(array('main'),'show'); ?>
		<?php //echo $html->js(array('libs/'.JS_FRAMEWORK,'application'),'show'); ?>

	</head>
	<body>
		<?php echo $content_for_layout; ?>
	</body>
</html>