<html>
<?php include tpl('email/css-styling')?>
<body>

<p>Liebe KollegInnen,</p>

<p>das ist der Überblick über unsere aktuellsten Texte der letzten 4 Tage. Alles über 2000 Klicks oder mindestens zwei Conversions können wir als tollen Erfolg verbuchen.
Bei allen anderen Texten unterhalb dieser Richtwerte müssen wir uns ehrlich die Frage beantworten: Lohnt es sich, weiter dieses Thema anzufassen?</p>

<table cellpadding="4" cellspacing="4" style="width:100%;">
<thead>
	<tr>
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

		<td><a href="<?=PAGEURL.'/artikel/'.$article['id']?>">
			<img heigh="20" width="80" src="<?=$article['image']?>"></a>
		</td>

		<td class="space-left space-right"><a href="<?=PAGEURL.'/artikel/'.$article['id']?>">
			<?=$article['kicker']?> - <?=$article['title']?></a></td>

		<td class="small"><?=ucfirst($article['ressort'])?></td>

		<?php if ($article['pageviews'] >= 2000): ?>
		<td class="center"><div class="pageviews"><?=number_format($article['pageviews'],0,'.','.')?></div></td>
		<?php else: ?>
		<td class="center"><?=number_format($article['pageviews'],0,'.','.')?></td>
		<?php endif ?>

		<?php if ($article['subscribers'] >= 700): ?>
		<td class="center"><div class="subscribers"><?=number_format($article['subscribers'],0,'.','.')?></div></td>
		<?php else: ?>
		<td class="center"><?=number_format($article['subscribers'],0,'.','.')?></td>
		<?php endif ?>


		<?php if ($article['conversions'] >= 2): ?>
		<td class="center"><div class="conversions"><?=$article['conversions']?></div></td>
		<?php else: ?>
		<td class="center"><?=$article['conversions']?></td>
		<?php endif ?>

		<?php if ($article['avgmediatime'] >= 160): ?>
			<td class="center"><div class="mediatime"><?=round($article['avgmediatime'])?></div></td>
		<?php else: ?>
			<td class="center"><?=round($article['avgmediatime'])?></td>
		<?php endif ?>


		<td class="space-left"><?=formatDate($article['pubdate'], "d.m.Y")?></td>
	</tr>
<?php endforeach ?>
</tbody>
</table>

<p>Alle Texte, die unter 500 Klicks einlaufen, gelten als Misserfolg und stehen ab sofort unter Beobachtung – dort haben wir offenbar keine Audience, für die sich der Einsatz unserer Arbeitskraft lohnt.</p>

<p>Lasst uns heute wie immer unser Bestes geben, dann kriegen wir das gemeinsam hin.<br />
Sport frei, Jan</p>

<p>-- <br>
Alle weiteren Daten im Compare-Tool: <a href="<?=PAGEURL?>">reports.lr-digital.de</a></p>


</body>
</html>
