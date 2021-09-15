<main class="detail-layout">

<article>
	<h3><?=$article['kicker'] ?? 'Dachzeile'?>&nbsp;<?php if ($article['plus']): ?><span class="plus-artikel bluebg">+</span><?php endif; ?></h3>
	<h1><?=$article['title']?></h1>
	<a class="noline" target="_blank" href="<?=$article['link']?>"><img style="border-radius:.2em;" src="<?=$article['image']?>"></a>
	<p class="condensed">Ressort: <b><?=ucwords($article['ressort'])?></b> | Artikel-ID: <b><?=$article['id']?></b> | Publiziert: <b><?=formatDate($article['pubdate'],"d.m.Y</b> H:i")?>&thinsp;Uhr</p>
	<p style="font-size:0.9em; line-height:130%;"><?=$article['description']?></p>

	<?php if ($emotions): ?>

		<figure style="margin:0 auto; margin-top:-2em; max-width:420px; max-height:220px; overflow:hidden;">
			<?=$emotions?>
		</figure>

	<?php endif; ?>

		<div class="text-center" style="z-index:10">
			<a class="button light" href="/artikel/<?=$article['id']?>/medium">Traffic Quellen</a>
			<a class="button light mt" href="/retresco/<?=$article['id']?>">Retresco Infos (BETA)</a>
		</div>

</article>

<section class="text-center">

	<figure style="overflow:hidden; max-height:270px;"><?php include tpl('charts/detail/radial_pageviews')?></figure>

	<?php if ($ressortAverage && $ressortAverage != 1): ?>
		<small>(Ø-Klicks im Ressort: <?=$ressortAverage?>)</small>
	<?php endif; ?>

	<hr style="width:40%"/>

	<?php if ($article['avgmediatime']): ?>
	<p>Ø-Mediatime: <span title="gesamt Mediatime: <?=round($article['mediatime'])?>&thinsp;s" class="greenbg"><?=round($article['avgmediatime'],2)?>&thinsp;s</span></p>
	<?php endif; ?>

	<p>
	<?php if ($article['subscribers']): ?>
	Plus-Leser-Anteil: <span title="Registrierte Leser: <?=$article['subscribers']?>" class="bluebg"><?=round($article['subscribers'] / $article['pageviews'] * 100)?>%</span>
	<?php endif; ?>

	<?php if ($article['buyintent']): ?>
	&ensp; Nutzer mit Kaufabsicht: <span class="orangebg"><?=$article['buyintent']?></span>
	<?php endif; ?>
	</p>


	<p>Ressort: <a href="/ressort/<?=urlencode($article['ressort'])?>"><?=ucwords($article['ressort'])?></a><br/>

		<?php if (auth_rights('type')): ?>
		Autor: <a href="/author/<?=urlencode($article['author'])?>"><?=$article['author']?></a></p>
		<?php else: ?>
		Autor: <?=$article['author']?></p>
		<?php endif ?>

	<?php if (auth_rights('type')): ?>
	<h3>Inhalts-Kategorie:</h3>
	<div style="text-align:center; max-width:380px; margin:0 auto; margin-bottom:1.5em;" class="dropdown-selectors">
		<select class="js-type-selector" data-id="<?=$article['id']?>">
				<option value="0">nicht vergeben</option>
				<?php if ($article['type']): ?>
				<option selected value="<?=$article['type']?>"><?=$article['type']?></option>
				<?php endif ?>
				<?php foreach (ARTICLE_TYPES as $type): ?>
				<?php if ($article['type'] == $type) {continue;} ?>
				<option value="<?=$type?>"><?=$type?></option>
				<?php endforeach ?>
		</select>
		<select class="js-tag-selector" data-id="<?=$article['id']?>">
				<option value="0">nicht vergeben</option>
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
	<p>Inhaltskategorie: <a href="/type/<?=urlencode(str_replace('/', '-slash-', $article['type']))?>"><?=$article['type']?></a>
	<?php if ($article['tag']): ?>(<?=$article['tag']?>)<?php endif; ?></p>
	<?php endif; ?>

	<?php if ($conversions): ?>
	<table class="lifetime-stats-table">
		<tr>
			<td>Kündigerquote:</td>
			<td><?=round((count($cancelled)/count($conversions) * 100),1) ?> %</td>
		</tr>
		<tr>
			<td>Ø-Abohaltedauer:</td>
			<td><?=$article['retention_days'] ?? '-'?> Tage</td>
		</tr>
	</table>
	<?php endif; ?>

	<hr style="width:40%"/>

	<figure style="overflow:hidden; max-height:300px;"><?php include tpl('charts/detail/radial_conversions_cancelled')?></figure>

</section>

<section>
	<?php if ($stats): ?>
	<h3>Artikelaufrufe:</h3>
	<?php include tpl('charts/linechart')?>

	<small class="mt text-right block">Letztes Update: <?=formatDate($article['refresh'], 'd.m.Y H:i')?> Uhr (<a class="" href="<?=$article['id']?>/refresh">jetzt aktualisieren</a>)</small>
	<?php else: ?>

	<h3>Detailinformationen:</h3>
	<p>Hinweis: Für diesen Artikel sind Momenten keine Google Analytics- oder Kündigungsdaten verfügbar. Am Erscheinungstag selbst sehen Sie stattdessen nahezu <a target="_blank" href="https://app5-eu.linkpulse.com/lp/login?redirect=%2Flp%2Fdashboard%3Fid%3D5c5451741f053112bf6fc45d">Echtzeitdaten</a> über Linkpulse.</p>
	<small class="mt text-right block">Letztes Update: <?=formatDate($article['refresh'], 'd.m.Y H:i')?> Uhr (<a class="" href="<?=$article['id']?>/refresh">jetzt aktualisieren</a>)</small>

	<?php endif; ?>
</section>

</main>

<?php if ($conversions): ?>
<main>

<hr >

<h1>Detailanalyse der Conversions</h1>

<section class="conversion-tables-layout">


<?php if (count($sources) > 0): ?>
<table class="fancy js-sortable">
	<thead><tr>
		<th>Quelle / Referrer</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($sources as $index => $count): ?>
	<tr>
		<td><?=ucfirst($index)?></td>
		<td><div<?php if ($count['active'] > 0): ?> class="conversions"<?php endif; ?>><?=$count['active']?></div></td>
		<td><div<?php if ($count['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$count['cancelled']?></div></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php endif ?>

<?php if (count($cities) > 0): ?>
<table class="fancy js-sortable">
	<thead><tr>
		<th>Städte</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($cities as $index => $count): ?>
	<tr>
		<td><?=ucfirst($index)?></td>
		<td><div<?php if ($count['active'] > 0): ?> class="conversions"<?php endif; ?>><?=$count['active']?></div></td>
		<td><div<?php if ($count['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$count['cancelled']?></div></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php endif ?>

<div>
<?php if (count($gender) > 0): ?>
<table class="fancy wide js-sortable">
	<thead><tr>
		<th>Geschlecht</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($gender as $index => $count): ?>
	<tr>
		<td><?=ucfirst($index)?></td>
		<td><div<?php if ($count['active'] > 0): ?> class="conversions"<?php endif; ?>><?=$count['active']?></div></td>
		<td><div<?php if ($count['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$count['cancelled']?></div></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php endif ?>

<?php if (count($payments) > 0): ?>
<table class="fancy wide js-sortable">
	<thead><tr>
		<th>Zahlmethode</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($payments as $index => $count): ?>
	<tr>
		<td><?=ucfirst($index)?></td>
		<td><div<?php if ($count['active'] > 0): ?> class="conversions"<?php endif; ?>><?=$count['active']?></div></td>
		<td><div<?php if ($count['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$count['cancelled']?></div></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php endif ?>

</div>

</section>


<hr />


<details>
	<summary>Einzelauflistung aller Käufe</summary>

<section class="detail-layout">

<?php foreach ($conversions as $conversion): ?>

<?php if ($conversion['cancelled']): ?>
<table class="fancy red">
<?php else: ?>
<table class="fancy">
<?php endif; ?>
	<tr>
		<th colspan="2">Kundeninfos</th>

	</tr>

	<tr>
		<td>PlenigoID:</td>
		<td><a target="_blank" href="https://backend.plenigo.com/h7DjbDhETLTvrgZLaZXA/customers/<?=$conversion['customer_id'];?>/show"><?=$conversion['customer_id'];?></a></td>
	</tr>
	<tr>
		<td>OrderID:</td>
		<td><a target="_blank" href="https://backend.plenigo.com/h7DjbDhETLTvrgZLaZXA/orders/<?=$conversion['order_id'];?>/show"><?=$conversion['order_id'];?></a></td>
	</tr>
	<tr>
		<td>Bestelldatum:</td>
		<td><?=formatDate($conversion['order_date'], 'd.m.Y H:i')?> Uhr</td>
	</tr>
	<tr>
		<td>Kündigungsdatum:</td>
		<td>
			<?php if ($conversion['cancelled']): ?>
			<?=formatDate($conversion['subscription_cancellation_date'], 'd.m.Y H:i')?> Uhr
			<br/>(Haltedauer: <?=$conversion['retention'];?> Tage)
			<?php else: ?>
			-
			<?php endif; ?>
		</td>
	</tr>

	<tr>
		<td>Produkt:</td>
		<td><?=$conversion['order_title'];?></td>
	</tr>
	<tr>
		<td>Bisherige Käufe:</td>
		<td><?=$conversion['subscription_count'] ?? '-';?></td>
	</tr>
	<tr>
		<td>Kaufpreis:</td>
		<td><?=$conversion['order_price'];?> Euro</td>
	</tr>
	<tr>
		<td>Quelle/Referrer:</td>
		<td><?=$conversion['ga_source'];?></td>
	</tr>
	<tr>
		<td>Bezahlmethode:</td>
		<td><?=$conversion['order_payment_method'];?></td>
	</tr>
	<tr>
		<td>Geschlecht:</td>
		<td><?=ucfirst($conversion['customer_gender'] ?? '-');?></td>
	</tr>
	<tr>
		<td>Stadt:</td>
		<td><?=$conversion['ga_city'];?>
		<?php if ($conversion['customer_city']): ?><br> (Plenigo: <?=$conversion['customer_city'];?>)<?php endif; ?>
		</td>
	</tr>

	<tr>
		<td>PLZ:</td>
		<td><?=$conversion['customer_postcode'] ?? '-';?></td>
	</tr>

	<tr>
		<td>Erfasste Sessions:</td>
		<td><?=$conversion['ga_sessions'];?> (bis Kaufentscheidung)</td>
	</tr>
</table>
<?php endforeach; ?>

</section>

</details>

</main>
<?php endif; ?>
