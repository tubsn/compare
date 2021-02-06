<main>

<div class="calendar-container">
	<div class="calendar-picker">
		<form action="/settimeframe" method="post">
			<select name="timeframe" class="js-timeframe">
				<?php if (session('timeframe') == 'Zeitraum'): ?>
				<option>gewählter Zeitraum</option>
				<?php endif; ?>
				<?php foreach (TIMEFRAMES as $timeframe): ?>
				<?php if (session('timeframe') == $timeframe): ?>
				<option selected><?=session('timeframe')?></option>
				<?php else: ?>
				<option><?=$timeframe?></option>
				<?php endif; ?>
				<?php endforeach ?>
			</select>
		</form>
		&thinsp;
		<form method="post" action="/settimeframe">
			<fieldset>
				<input type="date" name="from" value="<?=session('from')?>"> -
				<input type="date" name="to" value="<?=session('to')?>">
			</fieldset>
			<button class="calendar-button" type="submit"></button>
		</form>
	</div>
</div>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<?php if (isset($pageviews) || isset($conversions)): ?>

<p class="light-box" style="margin-bottom:2em;">
Artikel: <b><?=$numberOfArticles?></b> &emsp; Klicks: <b class="blue"><?=number_format($pageviews,0,',','.')?></b> &emsp; Conversions: <b class="orange"><?=$conversions?></b>
</p>

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
	<?php if (auth_rights('type')): ?><th>Autor</th><?php endif; ?>
	<th>Klicks</th>
	<th>Conv</th>
	<th>Datum</th>
</tr>
</thead>
<tbody>
<?php foreach ($articles as $article): ?>
<tr>
	<td class="narrower text-right"><?=$article['kicker']?> </td>
	<td><?php if ($article['plus']): ?><div class="bluebg">+</div><?php endif; ?></td>
	<td><a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a> </td>
	<td class="type">
		<?php if (auth_rights('type')): ?>
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
		<?php elseif ($article['type'] != ''): ?>
		<a href="/type/<?=urlencode(str_replace('/', '-slash-', $article['type']))?>"><?=$article['type']?></a>
		<?php else: ?>
		-
		<?php endif; ?>
	</td>
	<td class="nowrap"><a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $article['ressort']))?>"><?=ucwords($article['ressort'])?></a></td>
	<?php if (auth_rights('type')): ?>
	<td class="narrow"><a href="/author/<?=urlencode(str_replace('/', '-slash-', $article['author']))?>"><?=$article['author']?></a></td>
	<?php endif ?>
	<td class="text-right"><div<?php if ($article['pageviews'] > 2500): ?> class="pageviews"<?php endif; ?>><?=number_format($article['pageviews'],0,'.','.')?></div></td>
	<td class="text-right"><div<?php if ($article['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($article['conversions'],0,'.','.')?></div></td>
	<td data-sortdate="<?=$article['pubdate']?>" ><?=formatDate($article['pubdate'],"d.m.Y")?>&nbsp;<small><?=formatDate($article['pubdate'],"H:i")?>&nbsp;Uhr</small></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
	<p>Für diesen Zeitraum oder diese Suchanfrage sind keine Artikel vorhanden!</p>
<?php endif; ?>

</main>
