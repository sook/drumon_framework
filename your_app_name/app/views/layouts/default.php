<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="pt-br">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta http-equiv="content-language" content="pt-br">
		<meta charset="UTF-8">	
		<meta name="Keywords" content="">
		<meta name="Description" content="">
		<meta name="Author" content="">
		<meta name="csrf-token" content="<?php echo REQUEST_TOKEN; ?>">
		
		<title>Drumon Framework - <?php echo $html->block('title'); ?></title>
		
		<?php echo $html->styles(array('main')); ?>
		<?php echo $html->scripts(array('libs/'.JS_FRAMEWORK, 'application')); ?>

	</head>
	<body>
		<?php echo $content_for_layout; ?>
	</body>
</html>