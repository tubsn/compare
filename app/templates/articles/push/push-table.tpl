<?php if ($notifications): ?>
<table class="fancy wide list-table js-sortable js-collapse-table condensed">
<thead>
<tr>
	<th>Datum</th>
	<th>Zeit</th>
	<th style="text-align: right">Klickrate</th>
	<th style="text-align: center">Thema</th>
	<th>Push-Nachricht / Text</th>
	<th style="text-align: center">Audience</th>
	<th>Quelle</th>
	<th style="text-align: right">Pageviews</th>
	<th style="text-align: left">CV</th>
	<!--<th style="text-align: center">Status</th>-->
	<th style="text-align: right">OptOuts</th>
</tr>
</thead>
<tbody>
<?php foreach ($notifications as $notification): ?>
<tr>

	<td data-sortdate="<?=$notification['sentAt'] ?? $notification['queuedAt']?>" ><?=formatDate($notification['sentAt'] ?? $notification['queuedAt'],"d.m.Y")?></td>
	<td data-sortdate="<?=$notification['sentAt'] ?? $notification['queuedAt']?>" ><?=formatDate($notification['sentAt'] ?? $notification['queuedAt'],"H:i")?>&nbsp;Uhr</td>

	<?php if ($notification['clicks']): ?>
	<td class="text-right clickrate-level-<?=$notification['clickrate_level']?>"
		data-sortdate="<?=$notification['clickrate_sort']?>"
		title="Zugestellt: <?=gnum($notification['delivered'])?> | Klicks: <?=gnum($notification['clicks'])?>">
		<?=$notification['clickrate']?> %
	</td>
	<?php else: ?>
	<td data-sortdate="0" class="text-right">-</td>
	<?php endif ?>

	<?php if (isset($notification['article']['type'])): ?>
	<td class="text-center type"><a title="<?=$notification['article']['type']?>" class="type-link" href="/type/<?=urlencode(str_replace('/', '-slash-', $notification['article']['type']))?>"><?=$notification['article']['type']?></a></td>
	<?php else: ?>
	<td class="text-center">-</td>
	<?php endif ?>

	<td class=""><a href="/push/<?=$notification['id']?>"><?=$notification['title']?></a></td>

	<?php if (isset($notification['article']['audience'])): ?>
	<td class="text-center"><a title="<?=$notification['article']['audience']?>" class="audience-link" href="/audience/<?=urlencode(str_replace('/', '-slash-', $notification['article']['audience']))?>"><?=$notification['article']['audience']?></a></td>
	<?php else: ?>
	<td class="text-center">-</td>
	<?php endif ?>

	<td><a href="/artikel/<?=$notification['article_id'] ?? ''?>"><?=ucfirst($notification['article']['ressort'] ?? $notification['article_id'] ?? '-')?></a></td>

	<?php if (isset($notification['article']['pageviews']) && $notification['article']['pageviews'] > 2500): ?>
	<td class="text-right"><span class="pageviews"><?=gnum($notification['article']['pageviews'])?></span></td>
	<?php else: ?>
	<td class="text-right"><?=gnum($notification['article']['pageviews'] ?? 0)?></td>
	<?php endif ?>

	<?php if (isset($notification['article']['conversions']) && $notification['article']['conversions'] > 0): ?>
	<td class="text-left"><span class="conversions"><?=gnum($notification['article']['conversions'])?></span></td>
	<?php else: ?>
	<td class="text-left">-</td>
	<?php endif ?>

	<!--<td class="text-center"><?=ucfirst($notification['status'] ?? '-')?></td>-->

	<?php if ($notification['optOuts'] > 0): ?>
	<td class="text-right"><span class="cancelled"><?=gnum($notification['optOuts'])?></span></td>
	<?php else: ?>
	<td class="text-right">-</td>
	<?php endif ?>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
	<p>Für diesen Zeitraum oder diese Suchanfrage sind keine Pushnachrichten vorhanden!</p>
<?php endif; ?>

<style>
	.clickrate-level-0 {background-color: #B74242}
	.clickrate-level-1 {background-color: #EA7362}
	.clickrate-level-2 {background-color: #FFE0B8}
	.clickrate-level-3 {background-color: #F5FFAD}
	.clickrate-level-4 {background-color: #C6E377}
	.clickrate-level-5 {background-color: #90CA42}
</style>
