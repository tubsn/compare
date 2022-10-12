<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>


<?php if ($orders): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-left">
	<th class="text-left">OrderID</th>
	<th class="text-left" title="Plenigo Ansicht">Pln.</th>
	<th class="text-left">UserID</th>
	<th>Datum</th>
	<th>Uhrzeit</th>
	<th>Ursprung</th>
	<th>Produkt</th>
	<th style="text-align:right">Details</th>
	<th>Refresh</th>
</tr>
</thead>
<tbody>

<?php foreach ($orders as $order): ?>
<tr class="text-left">
	<td class="narrow text-left"><a href="/orders/<?=$order['order_id']?>"><?=$order['order_id']?></a></td>
	<td><a class="noline" target="_blank" title="In Plenigo öffnen" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/app-store/purchases/apple/<?=$order['order_id']?>/show">&#128194;</a></td>
	<td class="narrow" title="Drive Segment bei Bestellung: <?=$order['customer_order_segment']?>"><a href="/readers/<?=$order['customer_id']?>"><?=$order['customer_id']?></a></td>
	<td><?=formatDate($order['order_date'],'Y-m-d')?> <span class="hidden"><?=formatDate($order['order_date'],'H:i')?></span></td>
	<td><?=formatDate($order['order_date'],'H:i')?> Uhr</td>

	<td><?=$order['order_origin'] ?? 'Unbekannt'?></td>

	<td class="narrow"><?=$order['order_title']?></td>

	<td>
		<details><summary>Käufe</summary>
			<?php dump($order['sub_orders']);?>
		</details>

	</td>

	<td><?=formatDate($order['order_plenigo_refresh_date'], 'Y-m-d')?></td>


</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Conversions</h3>
<?php endif; ?>

</main>
