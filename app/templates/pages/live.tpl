<main>

<?php include tpl('navigation/date-picker');?>

<script>
let timeframe = document.querySelector('.js-timeframe');
timeframe.value = 'heute';
let from = document.querySelector('input[name="from"]');
from.value = null;
let to = document.querySelector('input[name="to"]');
to.value = null;
</script>

<h1>Live-Reporting: <b class="greenbg"><?=date('d.m.Y')?></b> &ensp;
Conversions: <b class="conversions"><?=count($orders)?></b>	&ensp;
Pageviews: <b class="pageviews"><?=number_format($pageviews,0,',','.')?></b>
</h1>

<p><b>Hinweis:</b> Diese Seite befindet sich aktuell im Aufbau!</p>

<?php include tpl('charts/livechart');?>

<h3>letzte eingehende Bestellungen</h3>

<table class="fancy js-sortable wide">
<thead>
	<tr>
		<th>ID</th>
		<th>Datum</th>
		<th>Produkt</th>
		<th>Preis</th>
		<th>Zahlart</th>
		<th>Details</th>
	</tr>
</thead>
<tbody>
<?php foreach ($orders as $order): ?>
	<tr>
		<td><a href="https://backend.plenigo.com/h7DjbDhETLTvrgZLaZXA/orders/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
		<td><?=$order['order_date']?></td>
		<td><?=$order['order_title']?></td>
		<td><?=$order['order_price']?></td>
		<td><?=$order['order_payment_method']?></td>
		<td><a href="/orders/<?=$order['order_id']?>">mehr</a></td>
	</tr>
<?php endforeach ?>
</tbody>
</table>


</main>
