<main>

<?php include tpl('navigation/date-picker');?>

<h1>Conversion Statistiken</h1>

<p></p>

<h2>Kündigungsquoten nach Kategorien</h2>
<section class="detail-layout" style="align-items:start">

<table class="fancy js-sortable">
	<thead><tr>
		<th>Artikel-Cluster</th>
		<th>Aktiv</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($types as $index => $count): ?>
	<tr>
		<td><?=$index?></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<table class="fancy js-sortable">
	<thead><tr>
		<th>Stadt</th>
		<th>Aktiv</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($cities as $index => $count): ?>
	<tr>
		<td><?=$index?></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>

<table class="fancy js-sortable">
	<thead><tr>
		<th>Referer</th>
		<th>Aktiv</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($sources as $index => $count): ?>
	<tr>
		<td><?=$index?></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>


<table class="fancy js-sortable">
	<thead><tr>
		<th>Ressort</th>
		<th>Aktiv</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($ressorts as $index => $count): ?>
	<tr>
		<td><?=$index?></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>


<table class="fancy js-sortable">
	<thead><tr>
		<th>Zahlmethoden</th>
		<th>Aktiv</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($payments as $index => $count): ?>
	<tr>
		<td><?=$index?></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<table class="fancy js-sortable">
	<thead><tr>
		<th>Geschlecht</th>
		<th>Aktiv</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($gender as $index => $count): ?>
	<tr>
		<td><?=$index?></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>


</section>


<section class="detail-layout">
	<div>
		<h1>Allgemein</h1>
		<?php if ($conversions): ?>
		<table class="fancy mb wide js-sortable">
		<thead>
		<tr class="text-right">
			<th>Artikel-Typ</th>
			<th>Ressort</th>
			<th>Author</th>
			<th>Datum</th>
			<th>Referer</th>
			<th>IP City</th>
			<th>Sessions</th>
			<th>Bezahlmethode</th>
			<th>Geschlecht</th>
			<th>Pl. City</th>
			<th>Gekündigt?</th>
			<th>Haltedauer</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($conversions as $conversion): ?>
		<tr class="text-right">
			<td><?=$conversion['article_type']?></td>
			<td><?=$conversion['article_ressort']?></td>
			<td><?=$conversion['article_author']?></td>
			<td><?=$conversion['order_date']?></td>
			<td><?=$conversion['ga_source']?></td>
			<td><?=$conversion['ga_city']?></td>
			<td><?=$conversion['ga_sessions']?></td>
			<td><?=$conversion['subscription_payment_method']?></td>
			<td><?=$conversion['customer_gender']?></td>
			<td><?=$conversion['customer_city']?></td>
			<td><?=$conversion['cancelled'] ?? '0'?></td>
			<td><?=$conversion['retention']?></td>
		</tr>
		<?php endforeach; ?>
		</tbody>
		</table>

		<?php else: ?>
		<h3>keine Conversions</h3>
		<?php endif; ?>
	</div>

</section>

</main>
