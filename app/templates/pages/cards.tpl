<main>

<?php include tpl('navigation/date-picker');?>




<?php if ($articles): ?>
<section class="card-layout">
<?php foreach ($articles as $article): ?>
<article class="article-card">

	<img style="width:300px;" src="<?=$article['image']?>">

	<div>
		<h3><?=$article['kicker']?></h3>

		<h1><?php if ($article['plus']): ?><div class="bluebg">+</div><?php endif; ?>
		<a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a>
		</h1>

		<?=$article['type']?>

		<a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $article['ressort']))?>"><?=ucwords($article['ressort'])?></a>
		<a href="/author/<?=urlencode(str_replace('/', '-slash-', $article['author']))?>"><?=$article['author']?></a>
		<div<?php if ($article['pageviews'] > 2500): ?> class="pageviews"<?php endif; ?>><?=number_format($article['pageviews'],0,'.','.')?></div>
		<div<?php if ($article['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($article['conversions'],0,'.','.')?></div>
		<?=formatDate($article['pubdate'],"d.m.Y")?>&nbsp;<small><?=formatDate($article['pubdate'],"H:i")?>&nbsp;Uhr</small>

	</div>

</article>
<?php endforeach; ?>
</section>
<?php else: ?>
	<p>Für diesen Zeitraum sind keine Artikel vorhanden!</p>
<?php endif; ?>

</main>
