<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>Übersicht aller erfasster Bestellungen seit ca. September 2020. Seit dem 23. März 2021 werden die Bestellungen direkt aus Plenigo importiert.</p>

<p class="light-box" style="margin-bottom:2em;">
Gesamtbestellungen: <span class="conversions"><?=$numberOfOrders?></span>
&emsp; davon Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; davon Extern: <b class="blue"><?=$externalOnly?></b>
&emsp; davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=round(($numberOfCancelled / $numberOfOrders) * 100)?>&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>


<?php if ($orders): ?>
<table class="fancy mb wide js-sortable compact font-small">
<thead>
<tr class="text-right">
	<th>ArtikelID</th>
	<th>Datum</th>
	<th>Preis</th>
	<th>Produkt</th>
	<th>Ressort</th>
	<th>Gekündigt</th>
	<th>Ø-Tage</th>
	<th>Geschlecht</th>
	<th>City</th>
	<th>PLZ</th>
	<th>Referer</th>
	<th>Bezahlmethode</th>
	<th>OrderID</th>
</tr>
</thead>
<tbody>


<?php foreach ($orders as $order): ?>
<tr class="text-right">
	<td><a href="/artikel/<?=$order['article_id']?>"><?=$order['article_id']?></a></td>
	<td><?=$order['order_date']?></td>
	<td><?=$order['order_price']?></td>
	<td class="narrow"><?=$order['subscription_internal_title'] ?? $order['order_title']?></td>
	<td><?=$order['article_ressort']?></td>
	<td><?=$order['cancelled'] ? 'ja' : '' ?></td>
	<td><?=$order['retention']?></td>
	<td><?=$order['customer_gender']?></td>
	<td class="narrow"><?=$order['customer_city'] ?? $order['ga_city']?></td>
	<td><?=$order['customer_postcode']?></td>
	<td class="narrow"><?=$order['ga_source']?></td>
	<td><?=$order['order_payment_method']?></td>
	<td class="narrow"><a href="https://backend.plenigo.com/***REMOVED***/orders/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Conversions</h3>
<?php endif; ?>

</main>
