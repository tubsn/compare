<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>


<p class="light-box" style="margin-bottom:2em;">
UTM-Klicks auf Landingpage: <b class="blue"><?=$campaignData->total()?></b>
&emsp; Klicks auf Bestellbutton: <b class="blue"><?=$clickData->total()?></b>
&emsp; Bestellungen im Shop: <b class="conversions"><?=count($orders ?? [])?></b>
&emsp; davon Gekündigt: <b class="redish"><?=count($cancelled ?? [])?></b>
&emsp; Kündigerquote: <b class="orange"><?=percentage(count($cancelled ?? []), count($orders ?? []))?>&thinsp;%</b>
&emsp; Conversionrate: <b class=""><?=percentage(count($orders ?? []), $campaignData->total(),3)?>&thinsp;%</b>
</p>




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
	<h3 class="text-center">Landingpage Aufrufe</h3>
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
	<h3 class="text-center">Bestellbutton Klicks</h3>
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

<h3>Shop Bestellungen (UTM Daten nicht verfügbar): <b class="conversions"><?=count($orders ?? [])?></b></h3>
<p>Bitte beachten, da wir keine UTM Daten Tracken, können die Bestellungen auch von anderen Kampagnen stammen.</p>

<?php if ($orders): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-left">
	<th class="text-left">OrderID</th>
	<th class="text-left">UserID</th>
	<th>Bestelldatum</th>
	<th>Uhrzeit</th>
	<th>Ursprung</th>
	<th>Produkt</th>
	<th>Bezeichnung</th>
	<th>Preis</th>
	<th>Bezahlmethode</th>
	<th>Gekündigt</th>
	<th>Ø-Haltedauer</th>
</tr>
</thead>
<tbody>

<?php foreach ($orders as $order): ?>
<tr class="text-left">
	<td class="narrow text-left"><a href="/orders/<?=$order['order_id']?>"><?=$order['order_id']?></a></td>
	<td class="narrow"><a href="/readers/<?=$order['customer_id']?>"><?=$order['customer_id']?></a></td>
	<td><?=formatDate($order['order_date'],'Y-m-d')?> <span class="hidden"><?=formatDate($order['order_date'],'H:i')?></span></td>
	<td><?=formatDate($order['order_date'],'H:i')?> Uhr</td>
	<td><?=$order['order_origin']?></td>
	<td class="narrow"><?=$order['order_title']?></td>
	<td class="narrow"><?=$order['subscription_internal_title'] ?? $order['order_title']?></td>
	<td><?=$order['order_price']?>&thinsp;€</td>
	<td><?=$order['order_payment_method']?></td>
	<td><?=$order['cancelled'] ? '<span class="cancelled">gekündigt</span>' : '' ?></td>
	<td data-sortdate="<?=$order['retention']?>"><?=(is_null($order['retention'])) ? '' : $order['retention'] . ' Tage' ?></td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Conversions</h3>
<?php endif; ?>



<details>
<summary>Alle weiteren UTM Shop - Transactions</summary>
<div style="display:flex; align-items:start; gap:2em;">
	<?=dump_table($conversions);?>
	<?=dump_table($conversionsGrouped);?>
</div>
</details>


</main>
