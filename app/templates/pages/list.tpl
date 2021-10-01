<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<?php if (isset($pageviews) || isset($conversions)): ?>

<p class="light-box" style="margin-bottom:2em;">
Artikel: <b><?=$numberOfArticles?></b> &emsp; Pageviews: <b class="blue"><?=number_format($pageviews,0,',','.')?></b>
&emsp; Subscribers: <b class="deepblue"><?=number_format($subscribers,0,',','.')?></b>
<?php if ($numberOfArticles > 0): ?>
&emsp; ⌀-Pageviews: <b class="blue"><?=number_format(($pageviews / $numberOfArticles), 0,',','.') ?></b>
<?php endif ?>
&emsp; ⌀-Mediatime: <b class="green"><?=number_format($avgmediatime,0,',','.')?>&thinsp;s</b>
&emsp; Kaufimpulse: <b class="orange"><?=$buyintents ?? '0'?></b>
&emsp; Conversions: <b class="conversions"><?=$conversions?></b>
&emsp; Kündiger: <b class="redish"><?=$cancelled ?? '0'?></b></p>

<?php endif; ?>


<?php if (isset($primaryChart)): ?>
<figure class="mb"><?=$primaryChart;?></figure>
<?php endif; ?>

<?php if (isset($secondaryChart)): ?>
<figure class="mb"><?=$secondaryChart;?></figure>
<?php endif; ?>

<?php if (isset($emotions)): ?>
	<?php foreach ($emotions as $id => $emo): ?>
		<?php if (isset($emo['emo_aerger'])): ?>
		<p><?=$id?> <?=$emo['emo_aerger']?></p>
		<?php endif ?>		
	<?php endforeach ?>
<?php endif ?>



<?php if ($articles): ?>
<table class="fancy wide js-sortable js-collapse-table condensed collapsed">
<thead>
<tr>
	<th>Dachzeile</th>
	<th></th>
	<th>Impuls</th>
	<th>Titel</th>
	<th>Inhaltstyp</th>
	<th>Ressort</th>
	<?php if (auth_rights('author')): ?><th>Autor</th><?php endif; ?>
	<?php if (!isset($showSubscribersInTable)): ?><th>Klicks</th><?php endif; ?>
	<?php if (isset($showSubscribersInTable) && $showSubscribersInTable == true): ?><th>Subs</th><?php endif; ?>
	<th>%-Subs</th>
	<th>⌀-MT</th>
	<th>Conv</th>
	<th>Künd</th>
	<?php if (isset($articles[0]['score'])): ?><th>SCR</th><?php endif ?>
	<th>Datum</th>
</tr>
</thead>
<tbody>
<?php foreach ($articles as $article): ?>
<tr>
	<td class="narrow text-right" title="<?=$article['kicker'] ?? '-'?>"><?=$article['kicker'] ?? '-'?> </td>
	<td><?php if ($article['plus']): ?><div class="bluebg"><a title="Statistik-Daten refreshen" class="noline" href="/artikel/<?=$article['id']?>/refresh">+</a></div><?php endif; ?></td>


	<?php if ($article['buyintent']): ?>
	<td title="Kaufabsichten: <?=$article['buyintent']?>">
		<div class="indicator buyintent">
			<?php if ($article['buyintent'] >= 33): ?>
			<div style="width:100%;"><?=$article['buyintent']?></div>
			<?php else: ?>
			<div style="width:<?=round($article['buyintent']*3)?>%;"><?=$article['buyintent']?></div>
			<?php endif; ?>
		</div>
	</td>
	<?php else: ?>
	<td><div class="indicator buyintent"><div>0</div></div></td>
	<?php endif; ?>

	<td><a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a> </td>

	<td class="type">
		<?php if (auth_rights('type')): ?>
		<div class="dropdown-selectors">
		<select class="js-type-selector" data-id="<?=$article['id']?>">
				<option value="0">...</option>
				<?php if ($article['type']): ?>
				<option selected value="<?=$article['type']?>"><?=$article['type']?></option>
				<?php endif ?>
				<?php foreach (ARTICLE_TYPES as $type): ?>
				<?php if ($article['type'] == $type) {continue;} ?>
				<option value="<?=$type?>"><?=$type?></option>
				<?php endforeach ?>
		</select>

		<select class="js-tag-selector" data-id="<?=$article['id']?>">
				<option value="0">...</option>
				<?php if ($article['tag']): ?>
				<option selected value="<?=$article['tag']?>"><?=$article['tag']?></option>
				<?php endif ?>
				<?php foreach (ARTICLE_TAGS as $tag): ?>
				<?php if ($article['tag'] == $tag) {continue;} ?>
				<option value="<?=$tag?>"><?=$tag?></option>
				<?php endforeach ?>
		</select>
		</div>
		<?php elseif ($article['type'] != ''): ?>
		<a class="type-link" href="/type/<?=urlencode(str_replace('/', '-slash-', $article['type']))?>"><?=$article['type']?></a>
		<?php if ($article['tag']): ?><br/><a class="type-link-light" href="/tag/<?=urlencode(str_replace('/', '-slash-', $article['tag']))?>"><?=$article['tag']?></a><?php endif; ?>
		<?php else: ?>
		-
		<?php endif; ?>
	</td>


	<td class="nowrap"><a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $article['ressort']))?>"><?=ucwords($article['ressort'])?></a></td>
	<?php if (auth_rights('author')): ?>
	<td class="narrow"><a href="/author/<?=urlencode(str_replace('/', '-slash-', $article['author'] ?? 'Unbekannt'))?>"><?=$article['author'] ?? 'Unbekannt'?></a></td>
	<?php endif ?>

	<?php if (!isset($showSubscribersInTable)): ?>
	<td class="text-right"><div<?php if ($article['pageviews'] > 2500): ?> class="pageviews"<?php endif; ?>><?=number_format($article['pageviews'],0,'.','.')?></div></td>
	<?php endif ?>

	<?php if (isset($showSubscribersInTable) && $showSubscribersInTable == true): ?>
	<td><div<?php if ($article['subscribers'] > 750): ?> class="subscribers"<?php endif; ?>><?=number_format($article['subscribers'],0,'.','.') ?? 0?></div></td>
	<?php endif; ?>

	<?php if ($article['pageviews'] && $article['subscribers'] && ($article['subscribers'] < $article['pageviews'])): ?>
	<td title="Plus-Leser: <?=$article['subscribers']?>">
		<div class="indicator plusleser">
			<div style="width:<?=round($article['subscribers'] / $article['pageviews'] * 100)?>%;"><?=round($article['subscribers'] / $article['pageviews'] * 100)?></div>
		</div>
	</td>
	<?php else: ?>
	<td><div class="indicator plusleser"><div>0</div></div></td>
	<?php endif; ?>

	<td><span class="<?php if ($article['avgmediatime'] > 150): ?>greenbg<?php endif; ?>"><?=number_format($article['avgmediatime'],0,'.','.') ?? 0?></span></td>

	<td class="text-right"><div<?php if ($article['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($article['conversions'],0,'.','.')?></div></td>


	<td class="narrower"><div<?php if ($article['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$article['cancelled'] ?? '-'?></div></td>

	<?php if (isset($article['score'])): ?>
	<td class="narrower text-right"><div class="score"><?=$article['score']?></div></td>
	<?php endif ?>

	<td data-sortdate="<?=$article['pubdate']?>" ><?=formatDate($article['pubdate'],"d.m.y")?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php if (count($articles) > 99) : ?>
<div class="text-center">
<button class="button js-collapse-table-btn">Komplette Tabelle einblenden</button>
</div>
<?php endif ?>

<?php else: ?>
	<p>Für diesen Zeitraum oder diese Suchanfrage sind keine Artikel vorhanden!</p>
<?php endif; ?>

</main>
