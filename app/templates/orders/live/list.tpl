<main style="position:relative">

<?php include tpl('orders/live/date-selection');?>

<h1><?=$page['title'] ?? 'Bestellungen'?>: <?=count($orders)?> | Umsatz: <?=gnum($revenue ?? 0,2)?> €</h1>

<table class="fancy js-sortable wide">
<thead>
	<tr>
		<th>ID</th>
		<th>Datum</th>
		<th>Produkt</th>
		<th>ProduktID</th>
		<th>Preis</th>
		<th>Zahlart</th>
		<th>Details</th>
	</tr>
</thead>
<tbody>
<?php foreach ($orders as $order): ?>
	<tr>
		<td><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/orders/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
		<td><?=	$order['order_date']?></td>
		<td><?=$order['order_title']?></td>
		<td><?=$order['subscription_internal_title']?></td>
		<td><?=$order['order_price']?></td>
		<td><?=$order['order_payment_method']?></td>
		<td><a href="/orders/<?=$order['order_id']?>">mehr</a></td>
	</tr>
<?php endforeach ?>
</tbody>
</table>

</main>
