<?php
	
	$locale = array (
		'lang' => 'pt-BR',
		'date' => array(
			'default' => '%d/%m/%Y %H:%M',
			'in_words' => '%d de %B de %Y',
			'months' => array(1=>'janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro'),
			'days' => array('domingo','segunda','terça','quarta','quinta','sexta','sábado')
		),
		
		'money' => array(
			'format' => array(2, ','), // 0,00
			'symbol' => "R$"
		),
		
		'next_page' => 'Próxima &raquo;',
		'prev_page' => '&laquo;Anterior',
		'first_page' => '<< Primeira página',
		'last_page' => 'Ultima página >>',
		
		'page_info' => array(
			'0' => 'Nenhum registro encontrado',
			'1' => 'Mostrando 1 registro',
			'all' => 'Mostrando todos os %value registros',
			'range' => 'Mostrando %from - %to de %all registros'
		)
	);
	
	setlocale(LC_ALL, 'portuguese', 'pt_BR', 'pt_br','pt-BR','pt-br', 'ptb_BRA','ptb','bra','portuguese-brazil','brazil','pt_BR.utf-8','br','pt_BR.iso-8859-1');
?>