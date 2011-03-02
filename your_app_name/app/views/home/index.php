<?php $html->block('title','Bem vindo'); ?>

<div id="app">
	<h1>Drumon<span>Framework</span></h1>
	<p>Desenvolvimento <span class="highlight">rápido</span> de aplicações web.</p>

	<h2>Bem Vindo</h2>
	<hr>
	<ul>
		<li>Todas as configurações de sua aplicação estão localizadas na pasta <span class="highlight">config/</span>.</li>
		<li>Para sua segurança altere o APP_SECRET em <span class="highlight">config/application.php</span> para <span class="highlight"><?php echo $app_secret?></span>.</li>
		<li>Altere a permissão da pasta <span class="highlight">public/images/cache</span> para 777 para usar o ImageHelper.</li>
		<li>Edite esta página em <span class="highlight">app/views/home/index.php</span>.</li>
	</ul>
	<hr>

	<p>Para maiores informações acesse o <a target="_blank" title="Ir para o wiki" href="http://github.com/sook/drumon_framework/wiki/">wiki</a> no github.</p>
	
</div>