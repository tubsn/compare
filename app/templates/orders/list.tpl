<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<?php if ($info): ?>
<p><?=$info?></p>
<?php endif; ?>

<p class="light-box" style="margin-bottom:2em;">
Gesamtbestellungen: <b><?=$numberOfOrders?></b>
&emsp; davon Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; davon Extern: <b class="blue"><?=$externalOnly?></b>
&emsp; davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=round(($numberOfCancelled / $numberOfOrders) * 100)?>&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>

<p>

Ungetrackte Conversions: <?=count($untracked)?>
</p>


<?php if ($orders): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-right">
	<th>OrderID</th>
	<th>Datum</th>
	<th>ArtikelID</th>
	<?php if (auth_rights('author')): ?><th>Author</th><?php endif; ?>
	<th>Ressort</th>
	<th>Gekündigt</th>
	<th>Haltedauer</th>
	<th>Produkt</th>
	<th>Preis</th>
	<th>Bezahlmethode</th>
	<th>Geschlecht</th>
	<th>City</th>
	<th>PLZ</th>
</tr>
</thead>
<tbody>



<?php foreach ($orders as $order): ?>
<tr class="text-right">
	<td><a href="https://backend.plenigo.com/***REMOVED***/orders/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
	<td><?=$order['order_date']?></td>
	<td><a href="/artikel/<?=$order['article_id']?>"><?=$order['article_id']?></a></td>
	<td><?=$order['article_ressort']?></td>
	<td><?=$order['cancelled'] ? 'ja' : '' ?></td>
	<td><?=$order['retention']?></td>
	<td><?=$order['order_title']?></td>
	<td><?=$order['order_price']?></td>
	<td><?=$order['order_payment_method']?></td>
	<td><?=$order['customer_gender']?></td>
	<td><?=$order['customer_city']?></td>
	<td><?=$order['customer_postcode']?></td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Conversions</h3>
<?php endif; ?>







</main>
