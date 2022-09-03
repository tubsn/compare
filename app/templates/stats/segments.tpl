<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>

<section class="stats-layout">


<div class="col-2" style="grid-template-columns: 1fr 1fr">

<figure class="mb">
	<h3 class="text-center">Anteil von Abonnenten an Gesamtnutzern</h3>
	<?=$charts->create([
		'metric' => $premiumUsersQuote['reg_quote'],
		'dimension' => $premiumUsersQuote['dimensions'],
		'color' => '#2F5772',
		'height' => 250,
		'legend' => 'top',
		'tickamount' => 12,
		'percent' => true,
		'area' => true,
		'ymax' => $maxChartHeight,
		'ymin' => 0,
		'name' => 'Anteil Abonnenten am Gesamttraffic',
		'template' => 'charts/default_line_chart',
	]);?>
</figure>


<figure class="mb">
	<h3 class="text-center">Ø-Aufkommen an Besuchern / Abonnenten am Tag</h3>
	<?=$charts->create([
		'metric' => [
			$premiumUsers['users'],
			$premiumUsers['subscribers'],
	     ],
		'dimension' => $premiumUsers['dimensions'],
		'color' => ['#2F5772', '#C52233'],
		'height' => 250,
		'legend' => 'top',
		'tickamount' => 12,
		'stacked' => false,
		'area' => false,
		'xfont' => '13px',
		'showValues' => false,
		'name' => ['Besucher pro Tag', 'Abonnenten pro Tag'],
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>


</div>

<figure class="mb">
	<?=$charts->create([
		'metric' => [
		  $segments['champions'],
		  $segments['high_usage_irregulars'],
		  $segments['loyals'],
		  $segments['low_usage_irregulars'],
		  $segments['flybys'],
		  //$segments['nonengaged'],
	     ],
		'dimension' => $segments['dimensions'],
		'color' => ['#923737', '#468C98', '#2F5772','#EABDA8', '#E5E5E5', '#B0C0C4', '#99B0BC'],
		'height' => 650,
		'legend' => 'top',
		'tickamount' => 30,
		'stacked' => false,
		'stackedTo100' => false,
		'area' => false,
		'showValues' => false,
		'name' => ['champions', 'high_usage_irregulars', 'loyals', 'low_usage_irregulars','fly-bys','nonengaged','unknown'],
		'template' => 'charts/default_line_chart',
	]);?>
</figure>


</section>

</main>
