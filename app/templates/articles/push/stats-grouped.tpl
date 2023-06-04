<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>


<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<div class="light-box" style="float:right">Kanäle filtern: <a class="button"href="?apponly">App</a> | <a class="button" href="?webonly">Web</a></div>

<p class="light-box" style="margin-bottom:2em;">
Pushmeldungen im Zeitraum: <b class="blue"><?=gnum($generalStats['amount'])?></b>
&emsp; Klickrate: <b class="orange"><?=$generalStats['clickrate']?>&thinsp;%</b>
</p>





<figure class="mb">
	<h3 class="text-center" style="margin:0">Klickraten zu Produktion</h3>
	<?=$charts->create([
		'metric' => [$stats['clickrate'], $stats['notifications']],
		'dimension' => $stats['dimensions'],
		'color' => ['#4C3A51','#E7AB79'],
		'height' => 450,
		'legend' => 'top',
		'stacked' => false,
		'showValues' => false,
		'name' => ['Klickrate', 'Pushmeldungen'],
		'template' => 'charts/default_bar_line_chart',
	]);?>
</figure>


<div class="col-2">
	<figure class="mb">
		<h3 class="text-center" style="margin:0">⌀-Klickrate</h3>
		<?=$charts->create([
			'metric' => $stats['clickrate'],
			'dimension' => $stats['dimensions'],
			'color' => '#B25068',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'percent' => true,
			'name' => 'Klickrate',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center" style="margin:0">⌀-Opt Outs</h3>
		<?=$charts->create([
			'metric' => $stats['avg_opt_outs'],
			'dimension' => $stats['dimensions'],
			'color' => '#E7AB79',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Opt-Outs',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

</div>




<div class="col-2">
	<figure class="mb">
		<h3 class="text-center" style="margin:0">Anzahl Pushmeldungen</h3>
		<?=$charts->create([
			'metric' => $stats['notifications'],
			'dimension' => $stats['dimensions'],
			'color' => '#4C3A51',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Pushmeldungen',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center" style="margin:0">⌀-an Nutzer ausgespielt</h3>
		<?=$charts->create([
			'metric' => $stats['avg_delivered'],
			'dimension' => $stats['dimensions'],
			'color' => '#774360',
			'height' => 450,
			'stacked' => false,
			'showValues' => false,
			'name' => 'Geliefert',
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

</div>



<!--
'metric' => [$timeStats['clickrate'], $timeStats['avg_per_day']],
'dimension' => $timeStats['dimensions'],
-->

<figure class="mb">
	<h3 class="text-center" style="margin:0">Zeitliche Entwicklung - Pushmeldungen pro Tag</h3>
	<?=$charts->create([
		'metric' => [$charts->cut($timeStats['clickrate'],1), $charts->cut($timeStats['avg_per_day'],1)],
		'dimension' => $charts->cut($timeStats['dimensions'],1),
		'color' => ['#4C3A51','#E7AB79'],		
		'height' => 450,
		'legend' => 'top',
		'stacked' => false,
		'showValues' => false,
		'name' => ['Klickrate', '⌀-Pushmeldungen pro Tag'],
		'template' => 'charts/default_bar_line_chart',
	]);?>
</figure>

<p class="text-center"><b>Achtung</b> die Pushdaten für die zeitliche Entwicklung werden nur manuell aktualisiert! Daten des aktuell laufenden Monats sind immer nur bedingt aussagefähig. Daten der Vormonate sind aber passend!</p>


</main>
