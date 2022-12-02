<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<style>
.calendar-timeframe, .calendar-datepicker {display:none;}
</style>


<?php if ($orders): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-left">
	<th class="text-left">OrderID</th>
	<th class="text-left">UserID</th>
	<th>Datum</th>
	<th>Uhrzeit</th>
	<th>Ursprung</th>
	<th>Produkt</th>
	<th style="text-align:left">Details</th>
	<th>Store-Refresh</th>
</tr>
</thead>
<tbody>

<?php foreach ($orders as $order): ?>
<tr class="text-left">

	<?php if ($order['order_origin'] == 'PLAYSTORE'): ?>
	<td class="narrow text-left"><a href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/app-store/purchases/google/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
	<?php else: ?>
	<td class="narrow text-left"><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/app-store/purchases/apple/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
	<?php endif; ?>

	<td class="narrow"><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/customers/<?=$order['customer_id']?>/show"><?=$order['customer_id']?></a></td>
	<td><?=formatDate($order['order_date'],'Y-m-d')?> <span class="hidden"><?=formatDate($order['order_date'],'H:i')?></span></td>
	<td><?=formatDate($order['order_date'],'H:i')?> Uhr</td>

	<?php if (strToLower($order['order_origin']) == 'playstore'): ?>
	<td><span class="stripe"><?=$order['order_origin'] ?? 'Unbekannt'?></span></td>
	<?php elseif (strToLower($order['order_origin']) == 'appstore'): ?>
	<td><span class="paypal"><?=$order['order_origin'] ?? 'Unbekannt'?></span></td>
	<?php endif; ?>

	<td class="narrow"><?=$order['order_title']?></td>

	<td>
		<details><summary>aufklappen</summary>
			<?php dump($order['sub_orders']);?>
		</details>

	</td>

	<td><?=formatDate($order['order_plenigo_refresh_date'], 'Y-m-d')?></td>


</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Bestellungen vorhanden</h3>
<?php endif; ?>



<?php if ($preOrders): ?>
<details>
<summary>Bestellungen vor Einführung der neuen App anzeigen</summary>

<?php $orders = $preOrders;?>	
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-left">
	<th class="text-left">OrderID</th>
	<th class="text-left">UserID</th>
	<th>Datum</th>
	<th>Uhrzeit</th>
	<th>Ursprung</th>
	<th>Produkt</th>
	<th style="text-align:left">Details</th>
	<th>Store-Refresh</th>
</tr>
</thead>
<tbody>

<?php foreach ($orders as $order): ?>
<tr class="text-left">

	<?php if ($order['order_origin'] == 'PLAYSTORE'): ?>
	<td class="narrow text-left"><a href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/app-store/purchases/google/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
	<?php else: ?>
	<td class="narrow text-left"><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/app-store/purchases/apple/<?=$order['order_id']?>/show"><?=$order['order_id']?></a></td>
	<?php endif; ?>

	<td class="narrow"><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/customers/<?=$order['customer_id']?>/show"><?=$order['customer_id']?></a></td>
	<td><?=formatDate($order['order_date'],'Y-m-d')?> <span class="hidden"><?=formatDate($order['order_date'],'H:i')?></span></td>
	<td><?=formatDate($order['order_date'],'H:i')?> Uhr</td>

	<?php if (strToLower($order['order_origin']) == 'playstore'): ?>
	<td><span class="stripe"><?=$order['order_origin'] ?? 'Unbekannt'?></span></td>
	<?php elseif (strToLower($order['order_origin']) == 'appstore'): ?>
	<td><span class="paypal"><?=$order['order_origin'] ?? 'Unbekannt'?></span></td>
	<?php endif; ?>

	<td class="narrow"><?=$order['order_title']?></td>

	<td>
		<details><summary>aufklappen</summary>
			<?php dump($order['sub_orders']);?>
		</details>

	</td>

	<td><?=formatDate($order['order_plenigo_refresh_date'], 'Y-m-d')?></td>


</tr>
<?php endforeach; ?>
</tbody>
</table>

</details>

<?php endif; ?>

</main>
