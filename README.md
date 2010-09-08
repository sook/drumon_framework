<ol>
	<li>
		<span>O que é Drumon Framework? Para que serve?</span>
		<p>	O Drumon Framework é um conjunto de código baseando na arquitetura MVC, a princípio este framework parece 
			ser inútil, porém ele foi desenvolvido com o objetivo de tornar simples a implementação de aplicações que 
			utilizam o sistema <a href="http://www.drumoncms.com/">Drumon</a>. 
		</p>
		<p>Para todos os módulos que estiverem disponíveis no Drumon CMS, existirá no framework um modelo relacionado, com todas as funcionalidades pré-definidas, 
			verifique na pasta **`core/`**. Por questão de simplicidade não está sendo utilizado nenhum outro framework, 
			porém, caso queira, está flexível para adaptação. Para isso, é necessário ter um conhecimento mais profundo 
			da linguagem PHP.
		</p>
		<p>
			Este projeto está sob licença GPL. Colabore com o desenvolvimento deste  <a href="http://github.com/sook/drumon_framework" target="_blank">projeto</a>.
		</p>
	</li>
	<li>
		<span>Baixe o framework do github:</span>
		<p>Para baixar o framework é simple, isso pode ser feito de duas formas:</p>
		<ol class="home_ol_inter">
			<li>
				<span>Baixando o projeto livre de versionamento:</span>
				<p>Acesse o site do projeto no endereço: <a href="http://github.com/sook/drumon_framework" target="_blank">http://github.com/sook/drumon_framework</a> e clique no botão 
					<img src="media/btdwnsource.png" alt=""> (localizado na parte superior direita da página). Descompacte
					 em uma pasta no diretório raiz do seu servidor que possui o php5 instalado.
				</p>
			</li>
			<li>
				<span>Baixando utilizando o comando git:</span>
				<p>Para isso é necessário que tenha instalado o controlador de versão do  <span class="destqhome">git</span>,
					 para instalar veja esse <a href="http://book.git-scm.com/2_installing_git.html">guia</a>. Após instalado, abra o terminal, acesse o diretório raiz do seu servidor 
					que possui o php5 instalado e digite o comando: 
				</p>
				<p><span class="home_prompt">git clone git@github.com:sook/drumon_framework.git</span></p>
			</li>
			
		</ol>
	</li>
	<li>
		<span>Configurando ambiente:</span>
		<p>
			Na pasta <span class="home_folder">app/config/</span> estão os principais arquivos a serem configurados. No arquivo <span class="home_folder">app/config/application.php</span>
			é possível definir as seguintes constantes: 
		</p>
		<ol class="home_ol_inter">
			<li><span class="grif_constants">ENV</span> - Ambiente de trabalho da aplicação: 'development' (default) ou 'production';</li>
			<li><span class="grif_constants">LANGUAGE</span> - Define a linguagem da aplicação: 'pt-br' (default) ou 'en-us';</li>
			<li><span class="grif_constants">DEFAULT_HELPERS</span> - Helpers que serão incluidos para toda a aplicação: 'Html,Date,Text' (default). 
				Para verificar os Helpers existentes, basta ir na pasta <span class="home_folder">core/helpers</span>.
			</li>
			<li><span class="grif_constants">ERROR</span> - Define a página default para erro 404. Este arquivo está localizado na pasta <span class="home_folder">app/public</span>.</li>
		</ol>
		<p>
			Para alterar as configurações do banco de dados vá na pasta <span class="home_folder">app/config/enviroments</span> e escolha o arquivo relacionado ao ambiente que está trabalhando.
			Pronto, agora basta acessar o endereço da página no seu browser <span class="home_folder">http://www.localhost/drumon_framework/app</span>. Para mais informações de como construir sua aplicação utilizando este framework, 
			acesse o guia de implementação do Drumon Framework, <a href="http://http://www.drumoncms.com/">clique aqui!</a>
		</p>
	</li>
</ol>