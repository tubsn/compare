<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>Eingehende Kündigungen aus Plenigo.</p>

<p class="light-box" style="margin-bottom:2em;">
Gesamtkündiger: <b class="redish"><?=count($cancellations)?></b>
&emsp; davon Loyals: <b title="Nutzer die im Kündigungsprozess ein neues Abo gekauft haben" class="blue"><?=$loyalOnly?></b>
&emsp; davon Champions: <b title="Nutzer die im Kündigungsprozess ein neues Abo gekauft haben" class="blue"><?=$championOnly?></b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>


<?php if ($cancellations): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-left">
	<th class="text-left">OrderID</th>
	<th class="text-left" title="Plenigo Ansicht">Pln.</th>
	<th class="text-left">UserID</th>
	<th title="Kündigungseingang">Datum</th>
	<th>Uhrzeit</th>
	<th>Ursprung</th>
	<th>Segment Kauf</th>
	<th>Produkt</th>
	<th>Kündigungsgrund</th>
	<th colspan="2" title="Mediatime in Kündigungswoche">Engagement pro Tag</th>
	<th style="display:none;"></th>
	<th>Segment Kündigung</th>
	<th>Haltedauer</th>
</tr>
</thead>
<tbody>

<?php foreach ($cancellations as $order): ?>
<tr class="text-left">
	<td class="narrow text-left"><a href="/orders/<?=$order['order_id']?>"><?=$order['order_id']?></a></td>
	<td><a class="noline" target="_blank" title="In Plenigo öffnen" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/orders/<?=$order['order_id']?>/show">&#128194;</a></td>
	<td class="narrow" title="Drive Segment bei Bestellung: <?=$order['customer_order_segment']?>"><a href="/readers/<?=$order['customer_id']?>"><?=$order['customer_id']?></a></td>
	<td title="Kaufdatum: <?=formatDate($order['order_date'],'Y-m-d H:i')?>"><?=formatDate($order['subscription_cancellation_date'],'Y-m-d')?> <span class="hidden"><?=formatDate($order['subscription_cancellation_date'],'H:i')?></span></td>
	<td><?=formatDate($order['subscription_cancellation_date'],'H:i')?>&nbsp;Uhr</td>
	<?php if (!empty($order['article_id'])): ?>
	<td><a href="/artikel/<?=$order['article_id']?>"><?=$order['order_origin']?></a></td>
	<?php else: ?>
	<td><?=$order['order_origin'] ?? 'Unbekannt'?></td>		
	<?php endif ?>

	<?php if ($order['customer_order_segment'] == 'champion'): ?>
	<td><span class="conversions"><?=ucfirst($order['customer_order_segment'] ?? 'Unbekannt')?></span></td>
	<?php else: ?>	
	<td><?=ucfirst($order['customer_order_segment'] ?? 'Unbekannt')?></td>
	<?php endif ?>

	<td class="narrow" title="Preis: <?=$order['order_price']?>&thinsp;€"><?=$order['order_title']?></td>
	<!--<td class="narrow"><?=$order['subscription_internal_title'] ?? $order['order_title']?></td>-->
	
	<td class="narrow">
		<?php if ($order['cancellation_reason'] == 3): ?>
		<span class="cancelled"><?=CANCELLATION_REASON[$order['cancellation_reason']] ?? '-'?></span>
		<?php else: ?>
		<?=CANCELLATION_REASON[$order['cancellation_reason']] ?? '-'?>
		<?php endif ?>
		<?php if ($order['subscription_status'] == 'IGNORED'): ?>
			<span class="conversions">umgewandelt</span>
		<?php endif ?>		
	</td>

	<?php
		$percent = percentage($order['customer_cancel_mediatime'] ?? 0,$mediatimeMax);
		if ($percent > 100) {$percent = 100;}
	?>

	<?php if (!empty($order['customer_cancel_mediatime'])): ?>
	<td data-sortdate="<?=$order['customer_cancel_mediatime']?>" style="padding-right:0; text-align:right">
		

		<?php if (round($order['customer_cancel_mediatime']/60/7,0) > 1): ?>
		<?=gnum($order['customer_cancel_mediatime']/60/7,0,'')?>&thinsp;min
		<?php else: ?>
		0&thinsp;min
		<?php endif ?>

	</td>

	<?php else: ?>
	<td></td>
	<?php endif ?>

	<td style="padding-right:1em; text-align:right; max-width:6%; width:100%" title="Wochen-Mediatime: <?=round($order['customer_cancel_mediatime'])?>&thinsp;s">
		<div class="indicator plusleser">
			<div style="width:<?=$percent?>%;"><?=$order['customer_cancel_mediatime']?></div>
		</div>
	</td>

	<?php if ($order['customer_cancel_segment'] == 'champion'): ?>
	<td><span class="conversions"><?=ucfirst($order['customer_cancel_segment'] ?? 'Unbekannt')?></span></td>
	<?php else: ?>	
	<td><?=ucfirst($order['customer_cancel_segment'] ?? 'Unbekannt')?></td>
	<?php endif ?>

	<td data-sortdate="<?=$order['retention']?>" class="text-right"><?=(is_null($order['retention'])) ? '' : $order['retention'] . ' Tage' ?></td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Conversions</h3>
<?php endif; ?>

</main>

