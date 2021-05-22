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
Artikel: <b><?=$numberOfArticles?></b> &emsp; Klicks: <b class="blue"><?=number_format($pageviews,0,',','.')?></b> 
<?php if ($numberOfArticles > 0): ?>
&emsp; ⌀-Klicks: <b class="blue"><?=number_format(($pageviews / $numberOfArticles), 0,',','.') ?></b>
<?php endif ?>
&emsp; Conversions: <b class="orange"><?=$conversions?></b> &emsp; Kündiger: <b class="redish"><?=$cancelled ?? '0'?></b></p>

<?php endif; ?>

<?php if (isset($chart) && strlen($chart['dates']) > 12): ?>
	<figure class="mb">
	<?php include tpl('charts/ressort_lines');?>
	</figure>
<?php endif; ?>

<?php if ($articles): ?>
<table class="fancy wide js-sortable condensed">
<thead>
<tr>
	<th>Dachzeile</th>
	<th></th>
	<th>Titel</th>
	<th>Inhaltstyp</th>
	<th>Ressort</th>
	<?php if (auth_rights('author')): ?><th>Autor</th><?php endif; ?>
	<th>Klicks</th>
	<th>+Leser</th>
	<th>Conv</th>
	<th>Künd</th>
	<th>Datum</th>
</tr>
</thead>
<tbody>
<?php foreach ($articles as $article): ?>
<tr>
	<td class="narrower text-right"><?=$article['kicker'] ?? '-'?> </td>
	<td><?php if ($article['plus']): ?><div class="bluebg"><a title="Statistik-Daten refreshen" class="noline" href="/artikel/<?=$article['id']?>/refresh">+</a></div><?php endif; ?></td>
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
		<?php if ($article['tag']): ?>(<a href="/tag/<?=urlencode(str_replace('/', '-slash-', $article['tag']))?>"><?=$article['tag']?></a>)<?php endif; ?>
		<?php else: ?>
		-
		<?php endif; ?>
	</td>
	<td class="nowrap"><a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $article['ressort']))?>"><?=ucwords($article['ressort'])?></a></td>
	<?php if (auth_rights('author')): ?>
	<td class="narrow"><a href="/author/<?=urlencode(str_replace('/', '-slash-', $article['author'] ?? 'Unbekannt'))?>"><?=$article['author'] ?? 'Unbekannt'?></a></td>
	<?php endif ?>
	<td class="text-right"><div<?php if ($article['pageviews'] > 2500): ?> class="pageviews"<?php endif; ?>><?=number_format($article['pageviews'],0,'.','.')?></div></td>

	<?php if ($article['pageviews'] && $article['subscribers']): ?>
	<td title="Plus-Leser: <?=$article['subscribers']?>">
		<div class="indicator plusleser">
			<div style="width:<?=round($article['subscribers'] / $article['pageviews'] * 100)?>%;"><?=round($article['subscribers'] / $article['pageviews'] * 100)?></div>
		</div>
	</td>
	<?php else: ?>
	<td><div class="indicator plusleser"><div>0</div></div></td>
	<?php endif; ?>

	<td class="text-right"><div<?php if ($article['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($article['conversions'],0,'.','.')?></div></td>
	<td class="narrower"><div<?php if ($article['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$article['cancelled'] ?? '-'?></div></td>
	<td data-sortdate="<?=$article['pubdate']?>" ><?=formatDate($article['pubdate'],"d.m.Y")?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
	<p>Für diesen Zeitraum oder diese Suchanfrage sind keine Artikel vorhanden!</p>
<?php endif; ?>

</main>
