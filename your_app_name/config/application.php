<?php

	// Ambiente da aplicação (development|production)
	define('ENV','development');
	
	// Linguagem da sua apicação.
	// Sete false para desabilitar.
	define('LANGUAGE','pt-BR');
	
	// Helpers carregados automaticamente.
	// define('AUTOLOAD_HELPERS','Html,Date,Text,Image,Movie,Paginate');
	define('AUTOLOAD_HELPERS','Html, Date, Text, Url');
	
	// Segredo da aplicação para proteção contra CSRF. !ALTERE ESSE VALOR!
	define('APP_SECRET','change-this-now');
	
	// Plugins utilizados em sua app.
	// define('PLUGINS','drumon_model,benchmark');
	define('PLUGINS','benchmark');
	
	// Javascript framework (mootools|jquery)
	// JQuery 1.4.4
	// Mootools 1.3.0.1
	define('JS_FRAMEWORK','jquery');
	
	// Configurações personalizadas de sua aplicação.
	
?>