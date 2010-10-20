<?
	/** 
	 * Ambiente da aplicação
	 * development - Usado durante desenvolvimento da sua aplicação.
	 * production - Usado quando sua aplicação está em produção.
	 */
	define('ENV','development');
	
	// Linguagem da sua apicação.
	// Sete false para desabilitar.
	define('LANGUAGE','pt-BR');
	
	// Helpers carregados automaticamente.
	// define('AUTOLOAD_HELPERS','[Html,Date,Text,Image,Movie,Paginate]');
	define('AUTOLOAD_HELPERS','Html, Date, Text, Url');
	
	// Set 404 Error page. 
	define('ERROR_404','404.html');
	
	// Plugins utilizados em sua app.
	define('PLUGINS','drumon_model');
	
	
	// Configurações personalizadas de sua aplicação.
	
?>