<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<?=$chart2?>
<?=$chart?>

<?php if ($articles): ?>
<table class="fancy wide list-table js-sortable js-collapse-table condensed collapsed">
<thead>
<tr>
	<th>Dachzeile</th>
	<th></th>
	<th>Reiz</th>
	<th>Titel</th>
	<th>Inhalt</th>
	<th>Audience</th>
	<th>Ressort</th>
	<th>D-Klicks</th>
	<th>Impressions</th>
	<th>CTR</th>
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
	<td data-sortdate="<?=$article['buyintent']?>" title="Kaufabsichten: <?=$article['buyintent']?>">
		<div class="indicator buyintent">
			<?php if ($article['buyintent'] >= 33): ?>
			<div style="width:100%;"><?=$article['buyintent']?></div>
			<?php else: ?>
			<div style="width:<?=round($article['buyintent']*3)?>%;"><?=$article['buyintent']?></div>
			<?php endif; ?>
		</div>
	</td>
	<?php else: ?>
	<td>
		<div class="indicator buyintent"><div>0</div></div>
	</td>
	<?php endif; ?>

	<td><a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a> </td>

	<td class="type">
		<?php if ($article['type'] != ''): ?>
		<a title="<?=$article['type']?>" class="type-link" href="/type/<?=urlencode(str_replace('/', '-slash-', $article['type']))?>"><?=$article['type']?></a>
		<?php if ($article['tag']): ?><br/><a title="<?=$article['tag']?>" class="type-link-light" href="/tag/<?=urlencode(str_replace('/', '-slash-', $article['tag']))?>"><?=$article['tag']?></a><?php endif; ?>
		<?php else: ?>
		-
		<?php endif; ?>
	</td>

	<?php if ($article['audience']): ?>
	<td><a title="<?=$article['audience']?>" class="audience-link" href="/audience/<?=urlencode(str_replace('/', '-slash-', $article['audience']))?>"><?=$article['audience']?></a></td>
	<?php else: ?>
	<td>-</td>
	<?php endif; ?>

	<td class="nowrap"><a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $article['ressort']))?>"><?=ucwords($article['ressort'])?></a></td>

	<td><?=number_format($article['discover_clicks'],0,'.','.')?></td>
	<td><?=number_format($article['discover_impressions'],0,'.','.')?></td>
	<td><?=$article['discover_ctr']?></td>




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
