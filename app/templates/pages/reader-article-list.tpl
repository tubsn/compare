	<?php if ($reader['articles_read']): ?>

	<hr/>

	<h3>Letzte Woche gelesene Artikel:</h3>
	<table class="fancy wide js-sortable condensed">
		<thead>
			<tr>
				<th>Dachzeile</th>
				<th></th>
				<th>Thumb</th>
				<th>Title</th>
				<th>Inhaltstyp</th>
				<th>Ressort</th>
				<th>Klicks</th>
				<th>⌀-MT</th>
				<th>Conv</th>
				<th>Kündiger</th>
				<th>Pubdate</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($reader['articles_read'] as $article): ?>
		<tr>
			<td class="narrower text-right"><?=$article['kicker'] ?? '-'?></td>
			<td><?php if (isset($article['plus']) && $article['plus']): ?><div class="bluebg">+</div><?php endif; ?></td>

<td><img style="width:70px; height:40px; object-fit: cover;" src="<?=$article['image'] ?? '/styles/img/flundr/no-thumb.svg' ?>"></td>

			<td><a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a></td>
			<td><?=$article['type'] ?? '-'?></td>
			<td><?=ucwords($article['ressort'] ?? '-')?></td>
			<td class="text-right"><div<?php if (isset($article['pageviews']) && $article['pageviews']> 2500): ?> class="pageviews"<?php endif; ?>><?=number_format($article['pageviews'] ?? 0,0,'.','.')?></div></td>
	<td><span class="<?php if ($article['avgmediatime'] > 150): ?>greenbg<?php endif; ?>"><?=number_format($article['avgmediatime'],0,'.','.') ?? 0?></span></td>

			<td class="text-right"><div<?php if (isset($article['conversions']) && $article['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($article['conversions'] ?? 0,0,'.','.')?></div></td>
	<td class="narrower"><div<?php if ($article['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$article['cancelled'] ?? '-'?></div></td>

			<td><?=formatDate($article['pubdate'],'d.m.Y')?></td>
		</tr>
		<?php endforeach; ?>
	</table>

	<?php endif ?>
