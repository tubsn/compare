<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>

<p>
</p>


<section class="stats-layout">


<figure class="mb">
	<h3 class="text-center">Segment Verteilung</h3>
	<?=$charts->create([
		'metric' => [$segments['champions'], $segments['high_usage_irregulars'], $segments['loyals'], $segments['low_usage_irregulars']],
		'dimension' => $segments['dimensions'],
		'color' => ['#1572A1', '#9AD0EC', '#EFDAD7', '#D885A3'],
		'height' => 450,
		'legend' => 'top',

		'stacked' => false,
		'showValues' => false,
		'name' => ['champions', 'high_usage_irregulars', 'loyals', 'low_usage_irregulars',],
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>


</section>

</main>
