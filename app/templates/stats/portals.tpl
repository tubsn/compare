<main>

<h1 class="text-center">Portalvergleich LR, MOZ und SWP</h1>
<p class="nt text-center">Hinweis: Für Einige Kennzahlen stehen alle Zeiträume zur Verfügung und sind daher abgeschnitten.</p>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:2em;">

	<figure class="mb">
		<h3 class="text-center">Bestelleingang Entwicklung</h3>
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




<div class="col-2" style="grid-template-columns: 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Bestelleingang Anteile</h3>
		<?=$charts->create([
			'metric' => [$lr['order']['orders'], $moz['order']['orders'], $swp['order']['orders']],
			'dimension' => $lr['order']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 300,
			'legend' => 'top',
			'showValues' => false,
			'stacked' => true,
			'stackedTo100' => true,
			'area' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Traffic Anteile (GA)</h3>
		<?=$charts->create([
			'metric' => [$lr['kpi']['pageviewsmio'], $moz['kpi']['pageviewsmio'], $swp['kpi']['pageviewsmio']],
			'dimension' => $lr['kpi']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 300,
			'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'stacked' => true,
			'stackedTo100' => true,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


</div>




<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Verhältnis von Bestellungen zu produzierten Artikeln</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['quotes']['articlesOrderQuote'],0),
			 			 $charts->cut($moz['quotes']['articlesOrderQuote'],0),
			  	 		 'null,' . $charts->cut_left($swp['quotes']['articlesOrderQuote'],1)],
			'dimension' => $charts->cut($swp['quotes']['dimensions'],0),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'animation' => false,
			'percent' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>

	<figure class="mb">
		<h3 class="text-center">Produzierte Artikel</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['articles'],7),
			 			 $charts->cut_left($moz['kpi']['articles'],7),
			  	 		 $charts->cut_left($swp['kpi']['articles'],7)],
			'dimension' => $charts->cut_left($swp['kpi']['dimensions'],7),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'showValues' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_bar_chart',
		]);?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Verhältnis von Aktiv Kunden zu produzierten Artikeln</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['quotes']['articlesActiveQuote'],0),
			 			 $charts->cut($moz['quotes']['articlesActiveQuote'],0),
			  	 		 'null,' . $charts->cut_left($swp['quotes']['articlesActiveQuote'],1)],
			'dimension' => $charts->cut($swp['quotes']['dimensions'],0),
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
			'animation' => false,
			'percent' => true,
			'showValues' => false,
			'name' => ['LR', 'MOZ', 'SWP'],
			'template' => 'charts/default_line_chart',
		]);?>
	</figure>


</div>




<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Plusartikel-Quote</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['quotePlus'],7),
			 			 $charts->cut_left($moz['kpi']['quotePlus'],7),
			  	 		 $charts->cut_left($swp['kpi']['quotePlus'],7)],
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
		<h3 class="text-center">Spielmacher-Quote</h3>
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
		<h3 class="text-center">Geister-Quote</h3>
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

	<figure class="mb">
		<h3 class="text-center">Score > 100-Quote</h3>
		<?=$charts->create([
			'metric' => [$charts->cut_left($lr['kpi']['quoteScore'],11),
			 			 $charts->cut_left($moz['kpi']['quoteScore'],11),
			  	 		 $charts->cut_left($swp['kpi']['quoteScore'],11)],
			'dimension' => $charts->cut_left($swp['kpi']['dimensions'],11),
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

<h1 class="text-center">Geografische Verteilung der Bestelldaten</h1>

<img src="/maps/vergleich-map-kuendiger-nov21.jpg">



<hr>

<h1 class="text-center">Bestell- und Kündigerdaten</h1>
<p class="nt text-center">Die Gesamtkündigungs-Quote wächst ganz natürlich mit zunehmender Zeit. Kongruent dazu sinkt die Menge der aktiv gehaltenden Kunden. <br/>Kündigerquoten nach X Tagen sind eingefrorene Kennzahlen. </p>


<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:2em;">

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
		<h3 class="text-center">Aktuell ungekündigte (Aktive-) Kunden</h3>
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
<h1 class="text-center">Aktive / Ungekündigte Kunden</h1>
<p class="nt text-center">Anzahl von Kunden, die nach einem gewissen Zeitraum (z.B. ihrem Probezeitraum), noch nicht gekündigt hatten. Bezogen auf den Bestellmonat des Kunden. <br>Beispiel: Von den Bestellern im August verblieben nach X-Tagen noch Y-Leser.</p>

<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr; margin-top:2em;">

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

	<figure class="mb">
		<h3 class="text-center">Anteil an aktiven Kunden nach 6 Monaten</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['quoteActiveAfter6M'],1),
			 			 $charts->cut($moz['order']['quoteActiveAfter6M'],1),
			  	 		 $charts->cut($swp['order']['quoteActiveAfter6M'],1)],
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
		<h3 class="text-center">Summe aktiver Kunden nach 6 Monaten</h3>
		<?=$charts->create([
			'metric' => [$charts->cut($lr['order']['activeAfter6M'],6),
			 			 $charts->cut($moz['order']['activeAfter6M'],6),
			  	 		 $charts->cut($swp['order']['activeAfter6M'],6)],
			'dimension' => $charts->cut($swp['order']['dimensions'],6),
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
			<th style="text-align:right">Aktiv nach 6M</th>
			<th style="text-align:right">Aktiv-Quote nach 6M</th>

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
			<td><?=$set['activeAfter6M'] ?? 0?></td>
			<td><?=$set['quoteActiveAfter6M'] ?? 0?>&thinsp;%</td>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
	</div>

<?php endforeach; ?>
</div>

</details>



</main>
