<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>

<p>
</p>


<section class="stats-layout">


<figure class="mb">
	<h3 class="text-center">Segment Verteilung</h3>
	<?=$charts->create([
		'metric' => [
		  $segments['daily_active_users'],
	     ],
		'dimension' => $segments['dimensions'],
		'color' => ['#1572A1'],
		'height' => 750,
		'legend' => 'top',
		'tickamount' => 30,
		'stacked' => false,
		'area' => false,
		'showValues' => false,
		'name' => ['daus'],
		'template' => 'charts/default_line_chart',
	]);?>
</figure>


<!--
<figure class="mb">
	<h3 class="text-center">Segment Verteilung</h3>
	<?=$charts->create([
		'metric' => [
		  $segments['nonengaged'],
		  $segments['flybys'],
		  $segments['champions'],
		  $segments['high_usage_irregulars'],
		  $segments['loyals'],
		  $segments['low_usage_irregulars']
	     ],
		'dimension' => $segments['dimensions'],
		'color' => ['#1572A1', '#9AD0EC', '#EFDAD7', '#D885A3', '#ddd','ccc'],
		'height' => 750,
		'legend' => 'top',
		'tickamount' => 30,
		'stacked' => false,
		'area' => false,
		'showValues' => false,
		'name' => ['fly-bys','nonengaged', 'champions', 'high_usage_irregulars', 'loyals', 'low_usage_irregulars'],
		'template' => 'charts/default_line_chart',
	]);?>
</figure>
-->

<!--
<figure class="mb">
	<h3 class="text-center">Segment Verteilung</h3>
	<?=$charts->create([
		'metric' => [$segments['champions_reg'], $segments['high_usage_irregulars_reg'], $segments['loyals_reg'], $segments['low_usage_irregulars_reg']],
		'dimension' => $segments['dimensions'],
		'color' => ['#1572A1', '#9AD0EC', '#EFDAD7', '#D885A3'],
		'height' => 750,
		'legend' => 'top',
		'tickamount' => 30,
		'stacked' => false,
		'area' => false,
		'showValues' => false,
		'name' => ['champions', 'high_usage_irregulars', 'loyals', 'low_usage_irregulars',],
		'template' => 'charts/default_line_chart',
	]);?>
</figure>
-->


</section>

</main>
