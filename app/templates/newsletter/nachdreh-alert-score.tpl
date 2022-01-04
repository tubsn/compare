<html>
<?php include tpl('email/css-styling')?>
<body>

<table cellpadding="10" style="width:100%; margin-bottom:20px; background-color:#eee;">
<tr>
	<td class="center"><b>Conversions: <span class="conversions"><?=$stats['conversions']?></span></b></td>
	<td class="center"><b>Pageviews: <span class="pageviews"><?=number_format($stats['pageviews'],0,'.','.')?></span></b></td>
	<td class="center"><b>Subscriberviews: <span class="subscribers"><?=number_format($stats['subscribers'],0,'.','.')?></span></b></td>
	<td class="center"><b>⌀-Mediatime: <span class="mediatime"><?=round($stats['avgmediatime'])?></span></b></td>
	<td class="right">Wochen-Ergebnis (letzte 7 Tage)</td>
</tr>
</table>

<br/>

<table cellpadding="4" cellspacing="4" style="width:100%;">
<thead>
	<tr>
		<th class="center">Score</th>
		<th>Bild</th>
		<th class="space-left">Artikel</th>
		<th>Ressort</th>
		<th class="center">Pageviews</th>
		<th class="center">Subscribers</th>
		<th class="center">Conversions</th>
		<th class="center">Mediatime</th>
		<th class="center">Pubdate</th>
	</tr>
</thead>
<tbody>
<?php foreach ($articles as $index => $article): ?>
	<tr <?php if ($index % 2): ?>style="background:#eee;"<?php endif ?>>

		<?php if ($article['score'] >= 150 ): ?>
		<td class="center nachdreh-green"><?=round($article['score'])?></td>
		<?php else: ?>
		<td class="center nachdreh-yellow"><?=round($article['score'])?></td>
		<?php endif; ?>

		<td><a href="<?=PAGEURL.'/artikel/'.$article['id']?>">
			<img heigh="20" width="80" src="<?=$article['image']?>"></a>
		</td>

		<td class="space-left space-right"><a href="<?=PAGEURL.'/artikel/'.$article['id']?>">
			<?=$article['kicker']?> - <?=$article['title']?></a></td>

		<td class="small"><?=ucfirst($article['ressort'])?></td>

		<?php if ($article['pageviews'] >= 5000): ?>
		<td class="center"><div class="pageviews"><?=number_format($article['pageviews'],0,'.','.')?></div></td>
		<?php else: ?>
		<td class="center"><?=number_format($article['pageviews'],0,'.','.')?></td>
		<?php endif ?>

		<?php if ($article['subscribers'] >= 400): ?>
		<td class="center"><div class="subscribers"><?=number_format($article['subscribers'],0,'.','.')?></div></td>
		<?php else: ?>
		<td class="center"><?=number_format($article['subscribers'],0,'.','.')?></td>
		<?php endif ?>


		<?php if ($article['conversions'] >= 3): ?>
		<td class="center"><div class="conversions"><?=$article['conversions']?></div></td>
		<?php else: ?>
		<td class="center"><?=$article['conversions']?></td>
		<?php endif ?>

		<?php if ($article['avgmediatime'] >= 160): ?>
			<td class="center"><div class="mediatime"><?=round($article['avgmediatime'])?></div></td>
		<?php else: ?>
			<td class="center"><?=round($article['avgmediatime'])?></td>
		<?php endif ?>

		<td class="space-left"><?=formatDate($article['pubdate'], "d.m.y")?></td>
	</tr>
<?php endforeach ?>
</tbody>
</table>

<p>Hinweis: In dieser Liste erscheinen Artikel der letzten 7 Publikations-Tage.</p>

<p>Alle weiteren Daten im Compare-Tool auf: <a href="<?=PAGEURL?>">reports.lr-digital.de</a></p>

<!--
<h3>Vielen Dank</h3>
<p>Ihr Compare-Team</p>
-->

</body>
</html>
