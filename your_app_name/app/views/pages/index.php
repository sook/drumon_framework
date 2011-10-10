<?php $html->block('title','Welcome'); ?>

<div id="app">
	<h1>Drumon<span>Framework</span></h1>
	<p><span class="highlight">Fast</span> web application development.</p>

	<h2>Welcome</h2>
	<hr>
	<ul>
		<li>Application configurations are in the folder <span class="highlight">config/</span>;</li>
		<li>For the safety of the application set APP_SECRET in <span class="highlight">config/application.php</span> to <span class="highlight"><?php echo $app_secret?></span>;</li>
		<li>Change <span class="highlight">public/images/cache</span> permission  to 777 for use ImageHelper;</li>
		<li>Edit this page in <span class="highlight">app/views/home/index.php</span>;</li>
	</ul>
	<hr>

	<p>For more information go to <a target="_blank" title="Ir para o wiki" href="http://github.com/sook/drumon_framework/wiki/">wiki</a> on github.</p>

</div>