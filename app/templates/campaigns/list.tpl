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

?>

<figure class="mb">
	<?=$charts->create([
		'metric' => $lpMetrics,
		'dimension' => $lpDimensions,
		'color' => '#6088b4',
		'height' => 300,
		'legend' => 'top',
		'area' => true,
		'showValues' => false,
		'name' => 'Kampagnen Aufrufe',
		'template' => 'charts/default_bar_chart',
	]);?>
</figure>


<h3>Sessions durch Kampagnen (nur UTM): <b class="conversions"><?=$campaignData->total()?></b></h3>

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
			<?php if ($dimension == 'campaign'): ?>
			<td><a href="/campaigns/filter/<?=$index?>"><?=$index?></a></td>
			<?php else: ?>
			<td><?=$index?></td>
			<?php endif; ?>
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

</main>
