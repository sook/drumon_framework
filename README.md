# Drumon Framework

É um framework para desenvolvimento rápido de aplicações web em PHP5+. Utiliza a arquitetura MVC e foi desenvolvido 
com o objetivo de tornar simples a implementação de aplicações que utilizam o [Drumon CMS](http://drumoncms.com/),
porém pode ser usado em aplicações sem o uso do Drumon CMS.

Você pode colaborar com o desenvolvimento deste projeto.

Este projeto está sob licença GPL. 


## Como instalar:

Baixe o Drumon Framework e o coloque em uma pasta do seu servidor com o php5+ instalado.

Pronto, agora basta acessar o endereço da página no seu browser **`http://localhost/your_app_name`**.

Para mais informações veja o [guia aqui](http://sook.github.com/drumon_framework/) do Drumon Framework.


## Configurando ambiente:

Na pasta **`app/config/`** estão os principais arquivos a serem configurados.

No arquivo **`app/config/application.php`** é possível definir as seguintes constantes:
	
  * ENV - Ambiente de trabalho da aplicação: 'development'(default) ou 'production';

  * LANGUAGE - Define a linguagem da aplicação: 'pt-BR'(default) ou 'en-US';

  * AUTOLOAD_HELPERS - Helpers que serão incluidos automaticamente ao iniciar a aplicação: 'Html,Date,Text,Url'(default). Para verificar os Helpers existentes, basta ir na pasta **`core/helpers`**.

Para alterar as configurações do banco de dados vá na pasta **`app/config/enviroments`** e escolha o arquivo relacionado ao ambiente que está trabalhando.


## Bugs & Sugestões

Por favor reporte os bugs ou dê sugestões através do [issue no GitHub](http://github.com/sook/drumon_framework/issues). Sua ajuda é apreciada.
