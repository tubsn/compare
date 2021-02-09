<main class="">

<section class="detail-layout">

<div>
	<h1>Conversion - Details (BETA!)</h1>
	<h3><?=$article['title']?></h3>

	<p class="condensed">Ressort: <b><?=ucwords($article['ressort'])?></b> | Artikel-ID: <b><?=$article['id']?></b> | Publiziert: <b><?=formatDate($article['pubdate'],"d.m.Y</b> H:i")?>&thinsp;Uhr</p>
	<p><?=$article['description']?></p>

	<p class="light-box">
	Klicks: <b class="blue"><?=number_format($article['pageviews'],0,',','.')?></b> &emsp; Besuche: <b class="blue"><?=number_format($article['sessions'],0,',','.')?></b> &emsp; Conversions: <b class="orange"><?=$article['conversions']?></b>
	</p>

	<p>Getrackte Conversions: <?=count($conversions)?></p>



</div>

<a class="noline" target="_blank" href="<?=$article['link']?>"><img style="border-radius:.2em;" src="<?=$article['image']?>"></a>

<div>
<p class="light-box">Inhaltskategorie: <a href="/type/<?=urlencode(str_replace('/', '-slash-', $article['type']))?>"><?=$article['type']?></a></p>
<table class="fancy">
	<tr>
		<th>Abo Kündigerquote</th>
		<th><?=number_format((count($cancelled)/count($conversions) * 100),2,',','.') ?> %</th>
	</tr>
	<tr>
		<td>Gekündigt:</td>
		<td><?=count($cancelled)?></td>
	</tr>
	<tr>
		<td>Ø-Haltedauer:</td>
		<td><?=$article['retention_days'] ?? '-'?> Tage</td>
	</tr>
</table>

<p><mark>Hinweis: Google liefert nur 1-2 Monate lang alle KäuferIDs. Möglicherweise fehlen bei älteren Artikeln also Conversions.</mark>
	<br><br>
	<a class="button" href="/artikel/<?=$article['id']?>/conversions/refresh">Conversions Refreshen</a>
</p>
</div>


</section>

<hr />

<section class="col-2" style="align-items:start">

<table class="fancy">
	<tr>
		<th>Quelle / Referrer</th>
		<th>Anzahl</th>
	</tr>
<?php foreach ($sources as $source => $count): ?>
	<tr>
		<td><?=$source?></td>
		<td><?=$count?></td>
	</tr>
<?php endforeach; ?>
</table>


<table class="fancy">
	<tr>
		<th>Städte laut IP</th>
		<th>Anzahl</th>
	</tr>
<?php foreach ($cities as $city => $count): ?>
	<tr>
		<td><?=$city?></td>
		<td><?=$count?></td>
	</tr>
<?php endforeach; ?>
</table>

</section>

<hr />

<h2>Einzelauflistung aller Käufe:</h2>
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
		<td><a target="_blank" href="https://backend-v2.plenigo.com/merchantBackend/customers/customer/<?=$conversion['customer_id'];?>"><?=$conversion['customer_id'];?></a> (<?=$conversion['external_customer_id'];?>)</td>
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
		<td><?=$conversion['subscription_count'];?></td>
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
		<td><?=$conversion['subscription_paymentMethod'];?></td>
	</tr>
	<tr>
		<td>Consent:</td>
		<td><?=$conversion['customer_consent'];?></td>
	</tr>
	<tr>
		<td>Gesperrt Status:</td>
		<td><?=$conversion['customer_status'];?></td>
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
		<td>Erfasste Sessions:</td>
		<td><?=$conversion['ga_sessions'];?> (bis Kaufentscheidung)</td>
	</tr>
</table>
<?php endforeach; ?>

</section>

</main>
