<?php

// Inicio Benchmark
if(BENCHMARK){
	include(CORE.'/class/Benchmark.php');
	Benchmark::start('Load Time');
}


Event::add('after_render','show_load_time');
function show_load_time() {
	if (BENCHMARK){
		echo '<style type="text/css">
					div.cms_debug{
					background-color: white;
					position: fixed;
					bottom:0;
					-moz-box-shadow:0 -1px 4px #000;
					box-shadow:0 -1px 4px #000;
					-webkit-box-shadow:0 -1px 4px #000;
					padding: 2px 4px 0 4px;
					left:10px;
					opacity:0.3;
				}
				div.cms_debug:hover{
					opacity:1;
				}
			</style>';
		Benchmark::stop('Load Time');
		echo '<div class="cms_debug">';
		foreach (Benchmark::get_totals() as $total) {
			echo $total.'<br>';
			}
			echo '</div>';
	}
}

?>