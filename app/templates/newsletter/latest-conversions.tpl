

<h1>Top5 Conversions | gestern bis jetzt | Gesamtbestellungen: <b class="conversions"><?=$conversionCount?></b></h1>

<?php if ($conversions): ?>
<table class="fancy wide js-sortable condensed mb">
<thead>
<tr>
	<th>Dachzeile</th>
	<th></th>
	<th>Thumb</th>
	<th>Titel</th>
	<th>Ressort</th>
	<th>Klicks</th>
	<th>+Leser</th>
	<th>MediaT</th>
	<th>Conv</th>
	<th>Publiziert</th>
</tr>
</thead>
<tbody>
<?php foreach ($conversions as $article): ?>
<tr>
	<td class="narrower text-right same-width"><?=$article['kicker'] ?? '-'?> </td>
	<td><?php if ($article['plus']): ?><div class="bluebg"><a title="Statistik-Daten refreshen" class="noline" href="/artikel/<?=$article['id']?>/refresh">+</a></div><?php endif; ?></td>
	<td><img style="width:70px; height:40px; object-fit: cover;" src="<?=$article['image'] ?? '/styles/img/flundr/no-thumb.svg' ?>"></td>
	<td><a href="/artikel/<?=$article['id'] ?? ''?>"><?=$article['title'] ?? 'AboShop oder Plusseite'?></a> </td>
	<td class="narrower ressort"><a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $article['ressort']))?>"><?=ucwords($article['ressort'])?></a></td>
	<td class="text-right"><div<?php if ($article['pageviews'] > 2500): ?> class="pageviews"<?php endif; ?>><?=number_format($article['pageviews'],0,'.','.')?></div></td>

	<?php if ($article['pageviews'] && $article['subscribers']): ?>
	<td class="text-right" title="Plus-Leser: <?=$article['subscribers']?>"><span class="subscribers nowrap"><?=round($article['subscribers'] / $article['pageviews'] * 100)?>&thinsp;%</span></td>
	<?php else: ?>
	<td class="text-right">0%</td>
	<?php endif; ?>

	<td class="text-right"><span class="<?php if ($article['avgmediatime'] > 150): ?>greenbg<?php endif; ?>"><?=number_format($article['avgmediatime'],0,'.','.') ?? 0?></span></td>
	<td class="text-right"><div<?php if ($article['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($article['conversions'],0,'.','.')?></div></td>
	<td data-sortdate="<?=$article['pubdate']?>" ><?=formatDate($article['pubdate'],"d.m.Y")?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
	<p>Für diesen Zeitraum oder diese Suchanfrage sind keine Artikel vorhanden!</p>
<?php endif; ?>
