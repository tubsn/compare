<main>

<?php include tpl('navigation/date-picker');?>

<h1>
Artikel Vergleich
</h1>

<hr/>



<div class="col-2" style="grid-template-columns: 1fr 1fr 1fr;">

	<?php foreach ($articles as $portal => $article): ?>

	<?php
		$portalURL = 'https://reports.lr-digital.de';
		if ($portal == 'SWP') {$portalURL = 'https://reports-swp.lr-digital.de';}
		if ($portal == 'MOZ') {$portalURL = 'https://reports-moz.lr-digital.de';}
	?>

	<div>
		<h1><?=$portal?></h1>

		<div class="mb">
		PV: <span class="bluebg"><?=$article['pageviews']?></span>
		MT: <span class="greenbg"><?=round($article['avgmediatime'])?>&thinsp;s</span>
		Subs: <span class="subscribers"><?=$article['subscribers']?></span>
		Conv: <span class="orangebg"><?=$article['conversions']?></span>
		</div>



		<article>
			<h3><?=$article['kicker'] ?? 'Dachzeile'?>&nbsp;<?php if ($article['plus']): ?><span class="plus-artikel bluebg">+</span><?php endif; ?></h3>
			<h1><?=$article['title']?></h1>
			<a class="noline" target="_blank" href="<?=$article['link']?>"><img style="border-radius:.2em;" src="<?=$article['image']?>"></a>
			<p class="condensed">Ressort: <b><?=ucwords($article['ressort'])?></b> | Artikel-ID: <b><?=$article['id']?></b> | Publiziert: <b><?=formatDate($article['pubdate'],"d.m.Y</b> H:i")?>&thinsp;Uhr</p>
			<p style="font-size:0.9em; line-height:130%;"><?=$article['description']?></p>

			<p><a href="<?=$portalURL?>/artikel/<?=$article['id']?>">zum Compare Artikel</a></p>

			<p>Tags:
			<?=$article['audience']?> <?=$article['type']?>	<?=$article['tag']?>
			</p>

		</article>
	</div>

	<?php endforeach; ?>

</div>


</main>
