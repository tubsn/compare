<main class="">

<?php include tpl('navigation/date-picker');?>

<h1>Käufe mit Usersegment</h1>


<p class="light-box" style="margin-bottom:2em;">

Gesamt Käufe: <b class="conversions"><?=count($completeOrders)?> </b>
&emsp;Käufe mit bekanntem Usersegment: <b class="blue"><?=count($orders)?> </b>
&emsp; Kündiger: <b class="redish"><?=count($cancelled)?> </b>
<?php if (count($orders) > 0): ?>
&emsp; Kündigerquote: <b class="orange"><?=round(count($cancelled) / count($orders) *100,2)?>&thinsp;%</b>
<?php endif ?>

</p>


<div class="box">
<select name="portal" onchange="window.location = '/readers/list/' + this.value">
	<option value="">Usersegment Wählen:</option>
	<option <?php if ($segment == 'champion'): ?>selected<?php endif ?>>champion</option>
	<option <?php if ($segment == 'fly-by'): ?>selected<?php endif ?>>fly-by</option>
	<option <?php if ($segment == 'low-usage-irregular'): ?>selected<?php endif ?>>low-usage-irregular</option>
	<option <?php if ($segment == 'high-usage-irregular'): ?>selected<?php endif ?>>high-usage-irregular</option>
	<option <?php if ($segment == 'loyal'): ?>selected<?php endif ?>>loyal</option>
	<option <?php if ($segment == 'non-engaged'): ?>selected<?php endif ?>>non-engaged</option>
	<option <?php if ($segment == 'unknown'): ?>selected<?php endif ?>>unknown</option>
</select>
</div>




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
	<th>UserSegment</th>
	<th>SegDate</th>
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
	<td class="narrow"><?=$order['user_segment']?></td>
	<td class="narrow"><?=$order['date']?></td>
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
