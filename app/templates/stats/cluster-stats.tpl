<main>

<?php include tpl('navigation/date-picker');?>
<h1><?=$page['title'] ?? 'Cluster Statistiken'?></h1>

<section class="cluster-table">
<?php foreach ($cluster as $name => $set): ?>
<div style="break-inside: avoid";>
<h3><?=$name?></h3>

	<table class="fancy wide js-sortable font-small compact mbig">
		<thead>
		<tr>
			<th>Ressort (Artikel)</th>
			<th style="text-align:right" title="Pageviews">PV</th>
			<th style="text-align:right" title="Subscriberviews">SV</th>
			<th style="text-align:right" title="Conversions">Conv</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($set as $ressort => $stats): ?>
		

			<tr>
				<td><?=ucwords($ressort)?> (<?=gnum($stats['articles'])?>)</td>
				<td data-sortdate="<?=$stats['pageviews']?>" class="text-right"><?=gnum($stats['pageviews'])?></td>
				<td data-sortdate="<?=$stats['subscriberviews']?>" class="text-right"><?=gnum($stats['subscriberviews'])?></td>
				<td data-sortdate="<?=$stats['conversions']?>" class="text-right"><?=gnum($stats['conversions'])?></td>
			</tr>
	<?php endforeach ?>
	</tbody>
	</table>

<hr>
</div>
<?php endforeach ?>

<?php if (empty($cluster)): ?>
	Keine Daten vorhanden!
<?php endif ?>


</section>

<style>
	.cluster-table {column-count: 1; margin-top:2em; width:100%;}
	@media only screen and (min-width: 800px) {.cluster-table {column-count: 2;}}
	@media only screen and (min-width: 1200px) {.cluster-table {column-count: 3;}}
	@media only screen and (min-width: 1800px) {.cluster-table {column-count: 4;}}
</style>

</main>
