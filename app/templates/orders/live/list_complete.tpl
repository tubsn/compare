<h1><?=$page['title'] ?? 'Bestellungen'?>: <?=count($orders)?></h1>

<p>Testseite</p>

<table class="fancy wide">
	<tr>
		<th>ID</th>
		<th>Datum</th>
		<th>Status</th>
		<th>Ressort</th>
		<th>Gekündigt</th>
		<th>Haltedauer</th>
		<th>Produkt</th>
		<th>Preis</th>
		<th>Zahlart</th>
		<th>Details</th>
	</tr>
<?php foreach ($orders as $order): ?>
	<tr>
		<td><a href="https://backend.plenigo.com/h7DjbDhETLTvrgZLaZXA/orders/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
		<td><?=$order['order_date']?></td>
		<td><?=$order['order_status']?></td>
		<td><?=$order['article_ressort']?></td>
		<td><?=$order['cancelled']?></td>
		<td><?=$order['retention']?></td>
		<td><?=$order['order_title']?></td>
		<td><?=$order['order_price']?></td>
		<td><?=$order['order_payment_method']?></td>
		<td><a href="/order/<?=$order['order_id']?>">mehr</a></td>
	</tr>
<?php endforeach ?>
</table>
