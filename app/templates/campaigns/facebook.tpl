<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<?php
$data = $campaignData->by_date();
ksort($data);
$lpDimensions = $charts->implode(array_keys($data));
$lpMetrics = $charts->implode($data);

$data = $clickData->by_date();
ksort($data);
$clickDimensions = $charts->implode(array_keys($data));
$clickMetrics = $charts->implode($data);
?>

<div class="col-2" style="grid-template-columns: 1fr 1fr;">

<figure class="mb">
	<?=$charts->create([
		'metric' => $lpMetrics,
		'dimension' => $lpDimensions,
		'color' => '#6088b4',
		'height' => 300,
		'legend' => 'top',
		'area' => true,
		'showValues' => false,
		'name' => 'Landingpage Aufrufe',
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>

<figure class="mb">
	<?=$charts->create([
		'metric' => $clickMetrics,
		'dimension' => $clickDimensions,
		'color' => '#314e6f',
		'height' => 300,
		'legend' => 'top',
		'area' => true,
		'showValues' => false,
		'name' => 'Button Klicks',
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>

</div>

<h3>Klicks auf die Landingpage (nur UTM): <b class="conversions"><?=$campaignData->total()?></b></h3>

<style>
.figures {display:flex;gap:1em; align-items:flex-start;}
.figures figure {flex-grow:1}
</style>


<div class="figures">
<?php $dimensions = ['campaign', 'source', 'medium'];?>
<?php foreach ($dimensions as $dimension): ?>

	<figure>
	<table class="fancy wide js-sortable">
	<thead>
	<tr>
		<th>UTM <?=ucfirst($dimension)?></th>
		<th style="text-align:right">Clicks</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($campaignData->totals_for($dimension) as $index => $value): ?>
		<tr>
			<td><?=$index?></td>
			<td style="text-align:right"><?=$value?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>

	<details>
		<summary>Details nach Datum</summary>
	<div style="display:flex;gap:1em; align-items:flex-start">
	<?php foreach ($campaignData->dimension($dimension) as $source => $days): ?>
	<table class="fancy wide js-sortable">
		<thead><tr>
			<th><?=$source?></th>
			<th>Klicks</th>
		</tr></thead>
		<?php foreach ($days as $date => $value): ?>
		<tr>
			<td><?=$date?></td>
			<td><?=$value?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endforeach; ?>
	</div>
	</details>
	</figure>

<?php endforeach; ?>
</div>


<hr>

<h3>Klicks auf den Bestellbutton: <b class="conversions"><?=$clickData->total()?></b></h3>

<div class="figures">
<?php $dimensions = ['eventlabel','campaign', 'source', 'medium'];?>
<?php foreach ($dimensions as $dimension): ?>

	<figure>
	<table class="fancy wide js-sortable">
	<thead><tr>
		<th>UTM <?=ucfirst($dimension)?></th>
		<th style="text-align:right">Clicks</th>
	</tr></thead>
	<?php foreach ($clickData->totals_for($dimension) as $index => $value): ?>
		<tr>
			<td><?=$index?></td>
			<td style="text-align:right"><?=$value?></td>
		</tr>
	<?php endforeach; ?>
	</table>

	<details>
		<summary>Details nach Datum</summary>
	<div style="display:flex;gap:1em; align-items:flex-start">
	<?php foreach ($clickData->dimension($dimension) as $source => $days): ?>
	<table class="fancy wide js-sortable">
		<thead><tr>
			<th><?=$source?></th>
			<th>Klicks</th>
		</tr></thead>
		<?php foreach ($days as $date => $value): ?>
		<tr>
			<td><?=$date?></td>
			<td><?=$value?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endforeach; ?>
	</div>
	</details>
	</figure>

<?php endforeach; ?>
</div>

<hr>
<h3>Alle Shop - Transactions</h3>
<div style="display:flex; align-items:start; gap:2em;">
	<?=dump_table($conversions);?>
	<?=dump_table($conversionsGrouped);?>
</div>


</main>
