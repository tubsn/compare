<main>

<?php include tpl('navigation/date-picker');?>

<script>
let timeframe = document.querySelector('.js-timeframe');
timeframe.value = 'heute';
let from = document.querySelector('input[name="from"]');
from.value = null;
let to = document.querySelector('input[name="to"]');
to.value = null;

function refresh() {    
    setTimeout(function () {
        location.reload()
    }, 300000);
}
refresh();

</script>




<h1>Live-Reporting: <b class="greenbg"><?=date('d.m.Y')?></b> &ensp;
Conversions: <b class="conversions"><?=count($orders)?></b>	&ensp;
Pageviews: <b class="pageviews"><?=number_format($pageviews,0,',','.')?></b>
</h1>

<?php include tpl('charts/livechart');?>

<?php if ($articles): ?>
<h3>Meist geklickte Artikel (Daten aus Linkpulse):</h3>

<table class="fancy js-sortable wide mbig">
<thead>
	<tr>
		<th>Ressort</th>
		<th>Thumb</th>
		<th>Titel</th>
		<th>Pageviews</th>
		<th>Conversions</th>
		<th>Subscribers</th>
		<th>Mediatime</th>
		<th>Artikel</th>
	</tr>
</thead>
<tbody>
<?php foreach ($articles as $article): ?>
	<tr>
		<td><?=ucfirst($article['ressort'])?></td>
		<td><img style="height: 35px;"src="<?=$article['image']?>"></td>
		<td><a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a></td>
		
		<?php if ($article['pageviews'] > 2500): ?>
		<td><span class="pageviews"><?=$article['pageviews']?></span></td>
		<?php else: ?>			
		<td><?=$article['pageviews']?></td>			
		<?php endif ?>

		<?php if ($article['conversions'] > 0): ?>
		<td><span class="conversions"><?=$article['conversions']?></span></td>
		<?php else: ?>
		<td><?=$article['conversions']?></td>
		<?php endif ?>

		<?php if ($article['subscribers'] > 100): ?>
		<td><span class="subscribers"><?=$article['subscribers']?></span></td>
		<?php else: ?>		
		<td><?=$article['subscribers']?></td>			
		<?php endif ?>		

		<?php if ($article['avgmediatime'] > 100): ?>
		<td><span class="greenbg"><?=$article['avgmediatime']?></span></td>
		<?php else: ?>			
		<td><?=$article['avgmediatime']?></td>			
		<?php endif ?>
		<td><a href="https://<?=$article['url']?>">öffnen</a></td>
	</tr>
<?php endforeach ?>
</tbody>
</table>
<?php endif ?>

<h3>Letzte eingehende Bestellungen (Daten aus Plenigo):</h3>
<?php if ($orders): ?>
<table class="fancy js-sortable wide mbig">
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
		<td><a href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/orders/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
		<td><?=$order['order_date']?></td>
		<td><?=$order['order_title']?></td>
		<td><?=$order['order_price']?></td>
		<td><?=$order['order_payment_method']?></td>
		<td><a href="/orders/<?=$order['order_id']?>">mehr</a></td>
	</tr>
<?php endforeach ?>
</tbody>
</table>
<?php else: ?>
<p>Aktuell keine Bestellungen</p>

<?php endif ?>

<div class="text-center"><b>Hinweis:</b> Diese Seite befindet sich aktuell im Aufbau!</div>

</main>
