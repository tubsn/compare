<main class="">

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>Übersicht erfasster UTM Kampagnen aus Google Analytics. (Nutzer ohne Tracking Consent sind ausgeschlossen)</p>

<p class="light-box" style="margin-bottom:2em;">
Kampagnenbestellungen: <b class="conversions"><?=$numberOfCampaigns?></b>
&emsp; davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=round(($numberOfCancelled / $numberOfCampaigns) * 100)?>&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>


<?php if ($campaigns): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-left">
	<th class="text-left">OrderID</th>
	<th>Bestelldatum</th>
	<th>Uhrzeit</th>
	<th>UTM-Source</th>
	<th>UTM-Medium</th>
	<th>UTM-Campaign</th>
	<th>Preis</th>
	<!--<th>Produkt</th>-->
	<th>Gekündigt</th>
	<th>Ø-Haltedauer</th>
	<th style="text-align:right">Ressort</th>
	<th style="text-align:right">ArtikelID</th>
</tr>
</thead>
<tbody>

<?php foreach ($campaigns as $campaign): ?>
<tr class="text-left">
	<td class="narrow text-left"><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/orders/<?=$campaign['order_id']?>/show"><?=$campaign['order_id']?></a></td>
	<!--<td class="narrow"><a href="/readers/<?=$campaign['customer_id']?>"><?=$campaign['customer_id']?></a></td>-->
	<td><?=formatDate($campaign['order_date'],'Y-m-d')?> <span class="hidden"><?=formatDate($campaign['order_date'],'H:i')?></span></td>
	<td><?=formatDate($campaign['order_date'],'H:i')?> Uhr</td>
	<td class="narrow"><?=$campaign['utm_source']?></td>
	<td class="narrow"><?=$campaign['utm_medium']?></td>
	<td style="max-width:350px" class="narrow"><?=$campaign['utm_campaign']?></td>

	<td><?=$campaign['order_price']?>&thinsp;€</td>
	<!--<td><?=$campaign['subscription_internal_title']?></td>-->
	<td><?=$campaign['cancelled'] ? '<span class="cancelled">gekündigt</span>' : '' ?></td>
	<td data-sortdate="<?=$campaign['retention']?>"><?=(is_null($campaign['retention'])) ? '' : $campaign['retention'] . ' Tage' ?></td>
	<td class="text-right"><?=ucfirst($campaign['article_ressort'])?></td>
	<td class="text-right"><a href="/artikel/<?=$campaign['article_id']?>"><?=$campaign['article_id']?></a></td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php else: ?>
<h3>keine Conversions</h3>
<?php endif; ?>

</main>
