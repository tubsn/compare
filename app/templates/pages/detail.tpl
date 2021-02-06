<main class="detail-layout">

<article>
	<h3><?=$article['kicker']?>&nbsp;<?php if ($article['plus']): ?><span class="plus-artikel">[+]</span><?php endif; ?></h3>
	<h1><?=$article['title']?></h1>
	<a class="noline" target="_blank" href="<?=$article['link']?>"><img style="border-radius:.2em;" src="<?=$article['image']?>"></a>
	<p class="condensed">Ressort: <b><?=ucwords($article['ressort'])?></b> | Artikel-ID: <b><?=$article['id']?></b> | Publiziert: <b><?=formatDate($article['pubdate'],"d.m.Y</b> H:i")?>&thinsp;Uhr</p>
	<p><?=$article['description']?><br/><br/> <a class="noline" target="_blank" href="<?=$article['link']?>">&#9657;&nbsp;Artikel auf LR-Online lesen</a></p>

	<p><a target="_blank" href="/retresco/<?=$article['id']?>">Retresco Info (Test!)</a></p>

</article>

<section class="text-center">

	<figure style="overflow:hidden; max-height:270px;"><?php include tpl('charts/radial_pageviews')?></figure>
	<small>(Ø-Klicks im Ressort: <?=$ressortAverage?>)</small>

	<hr style="width:40%"/>

	<table class="lifetime-stats-table">
		<tr>
			<td>Besuche/Sessions:</td>
			<td><?=$article['sessions']?></td>
		</tr>
	</table>
	<p>Ressort: <a href="/ressort/<?=urlencode($article['ressort'])?>"><?=ucwords($article['ressort'])?></a><br/>

		<?php if (auth_rights('type')): ?>
		Autor: <a href="/author/<?=urlencode($article['author'])?>"><?=$article['author']?></a></p>
		<?php else: ?>
		Autor: <?=$article['author']?></p>
		<?php endif ?>

	<?php if (auth_rights('type')): ?>
	<h3>Inhalts-Kategorie:</h3>
	<select style="text-align:center; width:200px; margin:0 auto; margin-bottom:1.5em;" class="js-type-selector" data-id="<?=$article['id']?>">
		<option value="0">nicht vergeben</option>
		<?php if ($article['type']): ?>
		<option selected value="<?=$article['type']?>"><?=$article['type']?></option>
		<?php endif ?>
		<?php foreach (ARTICLE_TYPES as $type): ?>
		<?php if ($article['type'] == $type) {continue;} ?>
		<option value="<?=$type?>"><?=$type?></option>
		<?php endforeach ?>
	</select>
	<?php elseif ($article['type'] != ''): ?>
	<p>Inhaltskategorie: <a href="/type/<?=urlencode(str_replace('/', '-slash-', $article['type']))?>"><?=$article['type']?></a></p>
	<?php endif; ?>

	<hr style="width:40%"/>

	<figure style="overflow:hidden; max-height:300px;"><?php include tpl('charts/radial_conversions')?></figure>

</section>

<section>
	<h3>Artikelaufrufe:</h3>
	<?php if ($stats): ?>

	<details>
		<summary style="position:absolute; right:0; top:0; cursor:pointer;">Details einblenden</summary>
			<table class="fancy wide js-sortable" style="margin-bottom:3em">
			<thead>
			<tr>
				<th>Date</th>
				<th>Klicks</th>
				<th>Besuche</th>
				<th>Conversions</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($stats as $day): ?>
			<tr>
				<td><?=$day['date']?></td>
				<td><?=$day['pageviews']?></td>
				<td><?=$day['sessions']?></td>
				<td><?=$day['conversions']?></td>
			</tr>
			<?php endforeach; ?>
			</tbody>
			</table>
	</details>

	<?php include tpl('charts/linechart')?>
	<small class="mt text-right block">Letztes Update: <?=formatDate($article['ga_refresh'], 'd.m.Y H:i')?> Uhr (<a class="" href="<?=$article['id']?>/refresh">jetzt aktualisieren</a>)</small>

	<?php else: ?>

	<p>Zur Zeit sind noch keine Analytics Daten vorhanden.<br/>
		Google Aktualisiert meist Mittags und Abends (<a href="https://app5-eu.linkpulse.com/lp/login?redirect=%2Flp%2Fdashboard%3Fid%3D5c5451741f053112bf6fc45d">zum Echtzeit Dashboard</a>)</p>
	<a class="button noline" href="<?=$article['id']?>/refresh">Analytics Daten laden</a>

	<?php endif; ?>
</section>

</main>
