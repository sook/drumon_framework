Drumon Framework
================================

O que é ?
-----------------------------------------
O Drumon Framework é um conjunto de código baseando na arquitetura MVC, a princípio este framework parece ser inútil, porém ele foi desenvolvido com o objetivo de tornar simples a implementação de aplicações que utilizam o sistema [Drumon](http://drumoncms.com/).


Para todos os módulos que estiverem disponíveis no Drumon CMS, existirá no framework um modelo relacionado, com todas as funcionalidades pré-definidas, verifique na pasta **`core/`**. Por questão de simplicidade não está sendo utilizado nenhum outro framework, porém, caso queira, está flexível para adaptação. Para isso, é necessário ter um conhecimento mais profundo da linguagem PHP.


Este projeto está sob licença GPL. Colabore com o desenvolvimento deste projeto.

Como instalar:
-------------------------------
Para baixar o framework é simple, isso pode ser feito de duas formas:


**Baixando o projeto livre de versionamento:**


Acesse o site do projeto no endereço: [http://github.com/sook/drumon_framework](http://github.com/sook/drumon_framework) e clique no botão ![Download Source](http://drumoncms.com.br/docs/api/media/btdwnsource.png "Source") (localizado na parte superior direita da página). Descompacte em uma pasta no diretório raiz do seu servidor que possui o php5 instalado.


**Baixando utilizando o comando git:**

Para isso é necessário que tenha instalado o controlador de versão do **git**, para instalar veja esse <a href="http://book.git-scm.com/2_installing_git.html">guia</a>. Após instalado, abra o terminal, acesse o diretório raiz do seu servidor que possui o php5 instalado e digite o comando:

**`git clone git@github.com:sook/drumon_framework.git`**

Configurando ambiente:
-------------------------
Na pasta **`app/config/`** estão os principais arquivos a serem configurados. No arquivo **`app/config/application.php`** é possível definir as seguintes constantes:
	
  * ENV - Ambiente de trabalho da aplicação: 'development' (default) ou 'production';

  * LANGUAGE - Define a linguagem da aplicação: 'pt-br' (default) ou 'en-us';

  * DEFAULT_HELPERS - Helpers que serão incluidos para toda a aplicação: 'Html,Date,Text' (default). Para verificar os Helpers existentes, basta ir na pasta **`core/helpers`**.

  * ERROR - Define a página default para erro 404. Este arquivo está localizado na pasta **`app/public`**.

Para alterar as configurações do banco de dados vá na pasta **`app/config/enviroments`** e escolha o arquivo relacionado ao ambiente que está trabalhando. Pronto, agora basta acessar o endereço da página no seu browser **`http://www.localhost/drumon_framework/app`**. Para mais informações de como construir sua aplicação utilizando este framework, acesse o [guia](http://www.drumoncms.com/) de implementação do Drumon Framework.