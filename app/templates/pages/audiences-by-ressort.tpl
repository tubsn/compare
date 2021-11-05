<main>
<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<table class="fancy mb wide condensed js-sortable">
<thead>
	<tr class="text-right">
		<th style="max-width:100px">Audience</th>
		<?php foreach ($ressortList as $ressort): ?>
		<th title="<?=ucfirst($ressort)?>" style="font-size:0.9em; padding:10px; text-align:center"><?=strtoupper(RESSORT_MAPPING[$ressort] ?? $ressort );?></th>
		<?php endforeach ?>
		<th>Summe</th>
	</tr>
</thead>
<tbody>

<?php foreach ($audiencesByRessort as $audience => $values): ?>
<tr>
	<td style="max-width:100px" class="text-right"><a href="/audience/<?=$audience?>"><?=$audience?></a></td>
	<?php foreach ($ressortList as $ressort): ?>
		<?php if (!empty($values[$ressort])): ?>
		<td class="text-center" style="padding:3px"><span class="num-articles"><?=$values[$ressort] ?? '-'?></span></td>
		<?php else: ?>
		<td class="text-center" style="padding:3px">-</td>
		<?php endif; ?>
	<?php endforeach ?>
	<td class="text-right"><b><?=$summedAudiences[$audience]?></b></td>
</tr>
<?php endforeach ?>

<tfoot>
<tr>
	<td style="max-width:100px" class="text-right">Summe</td>
	<?php foreach ($summedRessorts as $sum): ?>
	<?php if (is_null($sum['ressort'])) {continue;}?>
	<td class="text-center"><b><i><?=$sum['audience']?></i></b></td>
	<?php endforeach ?>
	<td class="text-right"><b><i><?=array_sum(array_column($summedRessorts,'audience'))?></b></i></td>
</tr>
</tfoot>

</tbody>
</table>


</main>
