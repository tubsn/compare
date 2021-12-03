<main>

<h1 class="text-center"><?=$page['title'] ?? 'Statistiken'?></h1>

<hr>

<h1 class="text-center">KPI Daten Portalweit</h1>

<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Pageviews Entwicklung in Millionen (GA)</h3>
		<?=$charts->create([
			'metric' => [$lr['kpi']['pageviewsmio'], $moz['kpi']['pageviewsmio'], $swp['kpi']['pageviewsmio']],
			'dimension' => $lr['kpi']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Traffic Anteil (GA)</h3>
		<?=$charts->create([
			'metric' => [$lr['kpi']['pageviewsmio'], $moz['kpi']['pageviewsmio'], $swp['kpi']['pageviewsmio']],
			'dimension' => $lr['kpi']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'stacked' => true,
			'stackedTo100' => true,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">⌀-Mediatime Entwicklung in Sekunden (GA)</h3>
		<?=$charts->create([
			'metric' => [$lr['kpi']['avgmediatime'], $moz['kpi']['avgmediatime'], $swp['kpi']['avgmediatime']],
			'dimension' => $lr['kpi']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr 1fr">
	<figure class="mb">
		<h3 class="text-center">Produzierte Artikel</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['articles'],3),
			 			 $charts->cut_left($moz['kpi']['articles'],3),
			  	 		 $charts->cut_left($swp['kpi']['articles'],3)],
			'dimension' => $charts->cut_left($swp['kpi']['dimensions'],3),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Produzierte Plus Artikel</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['plusarticles'],3),
			 			 $charts->cut_left($moz['kpi']['plusarticles'],3),
			  	 		 $charts->cut_left($swp['kpi']['plusarticles'],3)],
			'dimension' => $charts->cut_left($swp['kpi']['dimensions'],3),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Anteil-Spielmacher Artikel</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['quoteSpielmacher'],7),
			 			 $charts->cut_left($moz['kpi']['quoteSpielmacher'],7),
			  	 		 $charts->cut_left($swp['kpi']['quoteSpielmacher'],7)],
			'dimension' => $charts->cut_left($swp['kpi']['dimensions'],7),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'percent' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Anteil-Geister Artikel</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['quoteGeister'],7),
			 			 $charts->cut_left($moz['kpi']['quoteGeister'],7),
			  	 		 $charts->cut_left($swp['kpi']['quoteGeister'],7)],
			'dimension' => $charts->cut_left($swp['kpi']['dimensions'],7),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'percent' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>


<details>
<summary>Detaildaten Ausklappen</summary>

	<div class="" style="display:flex; gap:2em; margin-bottom:4em;">
	<?php foreach ($kpiData as $company => $data): ?>

		<div style="flex-grow:1;">
		<h3><?=$company?></h3>
		<table class="fancy wide">
			<thead>
				<th>Datum</th>
				<th style="text-align:right">Pageviews</th>
				<th style="text-align:right">Mediatime</th>
			</thead>
			<tbody>
		<?php foreach ($data as $month => $set): ?>
			<tr>
				<td><?=$month?></td>
				<td class="text-right"><?=number_format($set['pageviews'],0,',','.')?></td>
				<td class="text-right"><?=$set['avgmediatime']?>&thinsp;s</td>
			</tr>


		<?php endforeach; ?>
			</tbody>
		</table>
		</div>

	<?php endforeach; ?>
	</div>
</details>

<hr>

<h1 class="text-center">Bestelldaten - Geografische Verteilung</h1>

<img src="/maps/vergleich-map-kuendiger-nov21.jpg">



<hr>

<h1 class="text-center">Bestell- und Kündigerdaten</h1>

<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Eingehende Bestellungen</h3>
		<?=$charts->create([
			'metric' => [$lr['order']['orders'], $moz['order']['orders'], $swp['order']['orders']],
			'dimension' => $lr['order']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Aktive Kunden</h3>
		<?=$charts->create([
			'metric' => [$lr['order']['active'], $moz['order']['active'], $swp['order']['active']],
			'dimension' => $lr['order']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquoten Gesamt</h3>
		<?=$charts->create([
			'metric' => [$lr['order']['quote'], $moz['order']['quote'], $swp['order']['quote']],
			'dimension' => $lr['order']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>

<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Kündigerquoten am 1 Tag</h3>
		<?=$charts->create([
			'metric' => [$lr['order']['quoteChurnSameDay'], $moz['order']['quoteChurnSameDay'], $swp['order']['quoteChurnSameDay']],
			'dimension' => $lr['order']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Kündigerquoten nach 30 Tagen</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['quoteChurn30'],1),
			 			 $charts->cut($moz['order']['quoteChurn30'],1),
			  	 		 $charts->cut($swp['order']['quoteChurn30'],1)],
			'dimension' => $charts->cut($swp['order']['dimensions'],1),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Kündigerquoten nach 90 Tagen</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['quoteChurn90'],2),
			 			 $charts->cut($moz['order']['quoteChurn90'],2),
			  	 		 $charts->cut($swp['order']['quoteChurn90'],2)],
			'dimension' => $charts->cut($swp['order']['dimensions'],2),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

</div>


<hr>
<h1 class="text-center">Aktiv Kunden (NP - Kunden nach Probezeitraum)</h1>


<div class="col-2" style="grid-template-columns: 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Anteil an aktiven Kunden nach 30 Tagen</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['quoteActiveAfter30'],1),
			 			 $charts->cut($moz['order']['quoteActiveAfter30'],1),
			  	 		 $charts->cut($swp['order']['quoteActiveAfter30'],1)],
			'dimension' => $charts->cut($swp['order']['dimensions'],1),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Anteil an aktiven Kunden nach 90 Tagen</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['quoteActiveAfter90'],1),
			 			 $charts->cut($moz['order']['quoteActiveAfter90'],1),
			  	 		 $charts->cut($swp['order']['quoteActiveAfter90'],1)],
			'dimension' => $charts->cut($swp['order']['dimensions'],1),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'percent' => true,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


</div>

<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr">


	<figure class="mb">
		<h3 class="text-center">Summe aktiver Kunden nach 30 Tagen</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['activeAfter30'],1),
			 			 $charts->cut($moz['order']['activeAfter30'],1),
			  	 		 $charts->cut($swp['order']['activeAfter30'],1)],
			'dimension' => $charts->cut($swp['order']['dimensions'],1),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'percent' => false,
			'showValues' => false,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Summe aktiver Kunden nach 90 Tagen</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['activeAfter90'],3),
			 			 $charts->cut($moz['order']['activeAfter90'],3),
			  	 		 $charts->cut($swp['order']['activeAfter90'],3)],
			'dimension' => $charts->cut($swp['order']['dimensions'],3),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'percent' => false,
			'showValues' => false,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Anteile am Bestelleingang</h3>
		<?=$charts->create([
			'metric' => [$lr['order']['orders'], $moz['order']['orders'], $swp['order']['orders']],
			'dimension' => $lr['order']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'showValues' => false,
			'stacked' => true,
			'stackedTo100' => true,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>




</div>


<details>
<summary>Detaildaten Ausklappen</summary>

<div>
<?php foreach ($orderData as $company => $data): ?>

	<div>
	<h3><?=$company?></h3>
	<table class="fancy wide">
		<thead>
			<th>Datum</th>
			<th style="text-align:right">Bestellungen</th>
			<th style="text-align:right">Aktiv</th>
			<th style="text-align:right">Kündiger</th>
			<th style="text-align:right">Kündiger Tag 1</th>
			<th style="text-align:right">K-Quote</th>
			<th style="text-align:right">K-Quote 1 Tag</th>
			<th style="text-align:right">K-Quote 30 Tage</th>
			<th style="text-align:right">K-Quote 90 Tage</th>
			<th style="text-align:right">Aktiv nach 30</th>
			<th style="text-align:right">Aktiv-Quote nach 30</th>
			<th style="text-align:right">Aktiv nach 90</th>
			<th style="text-align:right">Aktiv-Quote nach 90</th>

		</thead>
		<tbody>
	<?php foreach ($data as $month => $set): ?>
		<tr class="text-right">
			<td class="text-left"><?=$month?></td>
			<td><?=$set['orders']?></td>
			<td><?=$set['active']?></td>
			<td><?=$set['cancelled']?></td>
			<td><?=$set['churnSameDay']?></td>
			<td><?=$set['quote']?>&thinsp;%</td>
			<td><?=$set['quoteChurnSameDay']?>&thinsp;%</td>
			<td><?=$set['quoteChurn30']?>&thinsp;%</td>
			<td><?=$set['quoteChurn90']?>&thinsp;%</td>
			<td><?=$set['activeAfter30'] ?? 0?></td>
			<td><?=$set['quoteActiveAfter30'] ?? 0?>&thinsp;%</td>
			<td><?=$set['activeAfter90'] ?? 0?></td>
			<td><?=$set['quoteActiveAfter90'] ?? 0?>&thinsp;%</td>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
	</div>

<?php endforeach; ?>
</div>

</details>



</main>
