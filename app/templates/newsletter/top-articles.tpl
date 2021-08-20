<main>

<style>
body {font-size:1.1em; max-width:1400px; margin:0 auto; margin-top:2em; }
h1 {font-size:1.2em;}
table td.narrower.same-width {max-width:170px}
table td.narrower.ressort {max-width:80px}
table.fancy td, table.fancy th {padding:4px 6px}
table.fancy {margin-bottom:3em;}
.logo-area {width:100%; text-align:right;}
.logo-area img {max-width:180px; margin-bottom:-2.5em;}
.fancy img {border-radius:5px}
.admin-board h3 {margin-bottom:0.4em;}
</style>

<div class="logo-area">
<a href="/"><img src="/styles/img/compare-logo-black.svg"></a>
</div>

<h1>Top 5 Pageviews (Publikationsdatum gestern)</h1>

<?php if ($pageviews): ?>
<table class="fancy wide js-sortable condensed mb">
<thead>
<tr>
	<th>Dachzeile</th>
	<th></th>
	<th>Thumb</th>
	<!--<th>Impuls</th>-->
	<th>Titel</th>
	<th>Ressort</th>
	<th>Klicks</th>
	<th>+Leser</th>
	<th>MediaT</th>
	<th>Conv</th>
	<!--<th>Künd</th>-->
	<th>Datum</th>
</tr>
</thead>
<tbody>
<?php foreach ($pageviews as $article): ?>
<tr>
	<td class="narrower text-right same-width"><?=$article['kicker'] ?? '-'?> </td>
	<td><?php if ($article['plus']): ?><div class="bluebg"><a title="Statistik-Daten refreshen" class="noline" href="/artikel/<?=$article['id']?>/refresh">+</a></div><?php endif; ?></td>

	<td><img style="width:80px;" src="<?=$article['image']?>"></td>

	<!--
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
	-->

	<td><a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a> </td>

	<td class="narrower ressort"><a href="/ressort/<?=urlencode(str_replace('/', '-slash-', $article['ressort']))?>"><?=ucwords($article['ressort'])?></a></td>

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

	<td class="text-right"><span class="<?php if ($article['avgmediatime'] > 150): ?>greenbg<?php endif; ?>"><?=number_format($article['avgmediatime'],0,'.','.') ?? 0?></span></td>

	<td class="text-right"><div<?php if ($article['conversions'] > 0): ?> class="conversions"<?php endif; ?>><?=number_format($article['conversions'],0,'.','.')?></div></td>
	<!--
	<td class="narrower"><div<?php if ($article['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$article['cancelled'] ?? '-'?></div></td>-->
	<td data-sortdate="<?=$article['pubdate']?>" ><?=formatDate($article['pubdate'],"d.m.Y")?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
	<p>Für diesen Zeitraum oder diese Suchanfrage sind keine Artikel vorhanden!</p>
<?php endif; ?>


<?php include tpl('newsletter/latest-conversions');?>

<br/><br/>

<?php include tpl('newsletter/article-list-for-mail');?>

</main>