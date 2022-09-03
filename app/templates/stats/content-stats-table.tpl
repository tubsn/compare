<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-right">
	<th>Artikel</th>
	<th class="text-left"><?=$class?></th>
	<th style="text-align:center">Plus-Artikel</th>
	<th>+Quote</th>
	<th>Klicks</th>
	<th>⌀-Klicks</th>
	<th style="text-align:center">%-Subs</th>
	<th>Subs</th>
	<th>⌀-Subs</th>
	<th>Mediatime</th>
	<th>⌀-MT</th>
	<th>K-Reiz</th>
	<th>Conv</th>
	<th>Convrate</th>
	<th>⌀-Conv</th>
	<th>Künd</th>
	<th>K-Quote</th>
</tr>
</thead>
<tbody>
<?php foreach ($groupedStats as $group => $stats): ?>
<tr class="text-right">
	<td title="davon Plusartikel: <?=$stats['plus']?>"><?=$stats['artikel']?></td>
	<td title="<?=ucwords($group)?>" class="narrower text-left"><a href="<?=$urlPrefix . urlencode(str_replace('/', '-slash-', $group))?>"><?=ucwords($group)?></a></td>

	<td title="Plusartikel: <?=$stats['plus']?>"><?=round($stats['plus'] / $stats['artikel'] * 100,1)?>&nbsp;%</td>

	<td title="Plusartikel: <?=$stats['plus']?>">
		<div class="indicator plusartikel">
			<div style="width:<?=round($stats['plus'] / $stats['artikel'] * 100)?>%;"><?=round($stats['plus'] / $stats['artikel'] * 100)?></div>
		</div>
	</td>

	<td title="Besuche: <?=$stats['sessions']?>"><div<?php if ($stats['pageviews'] > 0): ?> class="pageviews"<?php endif; ?>><?=number_format($stats['pageviews'],0,'.','.') ?? 0?></div></td>

	<td><?=number_format(($stats['pageviews'] / $stats['artikel']),0,'.','.') ?? 0?></td>

	<?php if ($stats['pageviews'] > 0): ?>
	<td title="Plus-Leser: <?=$stats['subscriberviews']?> (<?=round($stats['subscriberviews'] / $stats['pageviews'] * 100)?>%)">
		<div class="indicator plusleser">
			<div style="width:<?=round($stats['subscriberviews'] / $stats['pageviews'] * 100)?>%;"><?=round($stats['subscriberviews'] / $stats['pageviews'] * 100)?></div>
		</div>
	</td>
	<?php else: ?>
	<td><div class="indicator plusleser"><div>0</div></div></td>
	<?php endif; ?>

	<td><?=number_format($stats['subscriberviews'],0,'.','.') ?? 0?></td>
	<td><span class="<?php if (($stats['subscriberviews'] / $stats['artikel']) > 99): ?>subscribers<?php endif ?>"><?=number_format(($stats['subscriberviews'] / $stats['artikel']),0,'.','.') ?? 0?></span></td>



	<td title="Mediatime: <?=$stats['mediatime']?>">
		<div class="indicator mediatime">
			<div style="width:<?=round($stats['mediatime'] / $mediatime * 100)?>%;"><?=round($stats['mediatime'] / $mediatime * 100)?></div>
		</div>
	</td>

	<td><div<?php if ($stats['avgmediatime'] > 100): ?> class="greenbg"<?php endif; ?>><?=number_format($stats['avgmediatime'],0,'.','.') ?? 0?></div></td>


	<td><?=number_format($stats['buyintents'],0,'.','.') ?? 0?></td>


	<td><div<?php if ($stats['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($stats['conversions'],0,'.','.') ?? 0?></div></td>

	<?php if ($stats['sessions'] > 0): ?>
	<td><?=round($stats['conversions'] / $stats['sessions'] * 100,3)?>&nbsp;%</td>
	<?php else: ?><td>0&nbsp;%</td><?php endif; ?>

	<td><?=number_format(($stats['conversions'] / $stats['artikel']),3,'.','.') ?? 0?></td>

	<td><div<?php if ($stats['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$stats['cancelled'] ?? '0'?></div></td>

	<?php if ($stats['conversions'] > 0): ?>
	<td><?=round($stats['cancelled'] / $stats['conversions'] * 100,1)?>&nbsp;%</td>
	<?php else: ?><td>0&nbsp;%</td><?php endif; ?>



</tr>
<?php endforeach; ?>
</tbody>
</table>
