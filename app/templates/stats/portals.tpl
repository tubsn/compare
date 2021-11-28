<main>

<h1 class="text-center"><?=$page['title'] ?? 'Statistiken'?></h1>

<hr>


<h1 class="text-center">KPI Daten Portalweit</h1>

<div class="col-2" style="grid-template-columns: 1fr 1fr">

	<figure class="mb">
		<h3 class="text-center">Klick Entwicklung in Millionen (GA)</h3>
		<?=$charts->create([
			'metric' => [$lr['kpi']['pageviewsmio'], $moz['kpi']['pageviewsmio'], $swp['kpi']['pageviewsmio']],
			'dimension' => $lr['kpi']['dimensions'],
			'color' => ['#df886d', '#0967a8', '#e50046'],
			'height' => 350,
			'legend' => 'top',
			'area' => false,
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

<h1 class="text-center">Kundenverteilung</h1>

<img src="/maps/vergleich-map-nov21.png">



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
			'metric' => [$lr['order']['quoteChurn30'], $moz['order']['quoteChurn30'], $swp['order']['quoteChurn30']],
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
		<h3 class="text-center">Kündigerquoten nach 90 Tagen</h3>
		<?=$charts->create([
			'metric' => [$lr['order']['quoteChurn90'], $moz['order']['quoteChurn90'], $swp['order']['quoteChurn90']],
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
			<th style="text-align:right">Kündiger</th>
			<th style="text-align:right">Tag 1 Kündiger</th>
			<th style="text-align:right">Aktiv</th>
			<th style="text-align:right">Kündigerquote</th>
			<th style="text-align:right">Kündigerquote Tag1</th>
			<th style="text-align:right">Kündigerquote 30 Tage</th>
			<th style="text-align:right">Kündigerquote 90 Tage</th>
		</thead>
		<tbody>
	<?php foreach ($data as $month => $set): ?>
		<tr class="text-right">
			<td class="text-left"><?=$month?></td>
			<td><?=$set['orders']?></td>
			<td><?=$set['cancelled']?></td>
			<td><?=$set['churnSameDay']?></td>
			<td><?=$set['active']?></td>
			<td><?=$set['quote']?>&thinsp;%</td>
			<td><?=$set['quoteChurnSameDay']?>&thinsp;%</td>
			<td><?=$set['quoteChurn30']?>&thinsp;%</td>
			<td><?=$set['quoteChurn90']?>&thinsp;%</td>
		</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
	</div>

<?php endforeach; ?>
</div>

</details>



</main>
