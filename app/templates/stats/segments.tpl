<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>

<p>
</p>


<section class="stats-layout">


<div class="col-2" style="grid-template-columns: 1fr 1fr">

<figure class="mb">
	<h3 class="text-center">Anteil von Abonnenten an Gesamtnutzern</h3>
	<?=$charts->create([
		'metric' => $premiumUsersQuote['reg_quote'],
		'dimension' => $premiumUsersQuote['dimensions'],
		'color' => '#1572A1',
		'height' => 250,
		'legend' => 'top',
		'tickamount' => 12,
		'percent' => true,
		'area' => true,
		'name' => 'Anteil Abonnenten am Gesamttraffic',
		'template' => 'charts/default_line_chart',
	]);?>
</figure>

<figure class="mb">
	<h3 class="text-center">Ø-Aufkommen an Besuchern / Abonnenten am Tag</h3>
	<?=$charts->create([
		'metric' => [
			$premiumUsers['users'],
			$premiumUsers['users_reg'],
	     ],
		'dimension' => $premiumUsers['dimensions'],
		'color' => ['#1572A1', '#889EAF'],
		'height' => 250,
		'legend' => 'top',
		'tickamount' => 12,
		'stacked' => false,
		'area' => false,
		'xfont' => '13px',
		'showValues' => false,
		'name' => ['Besucher', 'Abonnenten'],
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
		  $segments['nonengaged'],
	     ],
		'dimension' => $segments['dimensions'],
		'color' => ['#506D84', '#D4B499', '#889EAF','#F3D5C0', '#8E7F7F', '#BBBBBB'],
		'height' => 450,
		'legend' => 'top',
		'tickamount' => 30,
		'stacked' => false,
		'stackedTo100' => false,
		'area' => false,
		'showValues' => false,
		'name' => ['champions', 'high_usage_irregulars', 'loyals', 'low_usage_irregulars','unknown', 'fly-bys','nonengaged',],
		'template' => 'charts/default_line_chart',
	]);?>
</figure>


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
