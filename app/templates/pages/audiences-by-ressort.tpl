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
		<th title="<?=ucfirst($ressort)?>" style="font-size:0.9em; padding:10px; text-align:center"><?=strtoupper(substr($ressort,0,3))?></th>
		<?php endforeach ?>
	</tr>
</thead>
<tbody>

<?php foreach ($audiencesByRessort as $audience => $values): ?>
<tr>
	<td style="max-width:100px" class="text-right"><?=$audience?></td>
	<?php foreach ($ressortList as $ressort): ?>
		<td class="text-center" style="padding:3px"><?=$values[$ressort] ?? '-'?></td>
	<?php endforeach ?>
</tr>
<?php endforeach ?>	

<tr>
	<td style="max-width:100px" class="text-right"><b>Summe</b></td>
	<?php foreach ($summedAudiences as $sum): ?>
	<td class="text-center"><b><i><?=$sum['audience']?></i></b></td>
	<?php endforeach ?>
</tr>
	
</tbody>
</table>


</main>
