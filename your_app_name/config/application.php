<?php

	// Ambiente da aplicação (development|production)
	define('ENV',isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'development');
	
	// Linguagem da sua apicação. (pt-BR|en-US|...)
	define('LANGUAGE','pt-BR');
	
	// Helpers carregados automaticamente
	// define('AUTOLOAD_HELPERS','Html,Date,Text,Image,Movie,Paginate');
	define('AUTOLOAD_HELPERS','Html, Date, Text, Url');
	
	// Segredo da aplicação para proteção contra CSRF. !ALTERE ESSE VALOR!
	define('APP_SECRET','altere-esse-valor-urgente');
	
	// Plugins utilizados em sua app
	// define('PLUGINS','drumon_model,benchmark');
	define('PLUGINS','benchmark');
	
	// Javascript framework (mootools|jquery)
	// JQuery 1.4.4
	// Mootools 1.3.0.1
	define('JS_FRAMEWORK','jquery');
	
	// Configurações personalizadas de sua aplicação
	
?>