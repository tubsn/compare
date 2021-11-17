<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>Übersicht erfasster Bestellungen. Seit dem 23. März 2021 werden die Bestellungen direkt aus Plenigo importiert.</p>

<p class="light-box" style="margin-bottom:2em;">
Gesamtbestellungen: <b class="conversions"><?=$numberOfOrders?></b>
&emsp; davon Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; davon Extern: <b class="blue"><?=$externalOnly?></b>
&emsp; davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=round(($numberOfCancelled / $numberOfOrders) * 100)?>&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>


<?php if ($orders): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-left">
	<th class="text-left">OrderID</th>
	<th class="text-left">UserID</th>
	<th>Bestelldatum</th>
	<th>Uhrzeit</th>
	<th>Ressort</th>
	<th>Produkt</th>
	<th>Bezeichnung</th>
	<th>Preis</th>
	<th>Bezahlmethode</th>
	<th>Gekündigt</th>
	<th>Ø-Haltedauer</th>
	<th style="text-align:right">ArtikelID</th>
</tr>
</thead>
<tbody>

<?php foreach ($orders as $order): ?>
<tr class="text-left">
	<td class="narrow text-left"><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/orders/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
	<td class="narrow"><a href="/readers/<?=$order['customer_id']?>"><?=$order['customer_id']?></a></td>
	<td><?=formatDate($order['order_date'],'Y-m-d')?> <span class="hidden"><?=formatDate($order['order_date'],'H:i')?></span></td>
	<td><?=formatDate($order['order_date'],'H:i')?> Uhr</td>
	<td><?=ucfirst($order['article_ressort'])?></td>
	<td class="narrow"><?=$order['order_title']?></td>
	<td class="narrow"><?=$order['subscription_internal_title'] ?? $order['order_title']?></td>
	<td><?=$order['order_price']?>&thinsp;€</td>
	<td><?=$order['order_payment_method']?></td>
	<td><?=$order['cancelled'] ? '<span class="cancelled">gekündigt</span>' : '' ?></td>
	<td data-sortdate="<?=$order['retention']?>"><?=(is_null($order['retention'])) ? '' : $order['retention'] . ' Tage' ?></td>
	<td class="text-right"><a href="/artikel/<?=$order['article_id']?>"><?=$order['article_id']?></a></td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Conversions</h3>
<?php endif; ?>

</main>
