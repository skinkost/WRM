<div class="ui section divider">
 </div>

<div class="ui three column centered grid">
	<div class="center aligned column">
	<?php
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $start), 4);
		echo 'Laddtid: '.$total_time.' sekunder.';
	?>
	</div>
	<div class="center aligned column">White Red AB 🐵 2017</div>
	<div class="center aligned column">Version 0.67 <?php var_dump($gUsers); ?></div>
</div>
<script>

$('.ui.dropdown').dropdown();

</script>