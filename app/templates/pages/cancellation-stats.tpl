<main>

<?php include tpl('navigation/date-picker');?>

<h1>Kündigungsquoten nach Kategorien</h1>

<p><b>Kündigungsdaten sind NICHT Live</b> und beziehen sich auf den Tag der letzten Statistik-Aktualisierung des Artikels.<br />
Die Kündigerraten sind vor Februar 2021 nicht relevant, da hier zuviele Daten fehlen!</p>

<p>Die Kündigerdaten beziehen sich hier auf den ausgewählten Zeitraum und <b>nicht auf das Publikationsdatum</b> des Artikels.<br/>Es kommt daher zu <b>Abweichungen im Vergleich zur Statistik-Seite</b> (dort wird nach Artikel Publikationsdatum gemessen).</p>

<p class="light-box" style="margin-bottom:2em;">

Conversions: <b class="orange"><?=count($conversions)?></b>
&emsp; Gekündigt: <b class="redish"><?=count($cancelled)?></b>
&emsp; Kündigungsquote: <b class=""><?=round($quote,2)?> %</b>
</p>

<hr>

<section class="detail-layout" style="align-items:start">

<div>
<table class="fancy wide js-sortable">
	<thead><tr>
		<th>Zahlmethoden</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($payments as $index => $count): ?>
	<tr>
		<td><?=ucfirst(strtolower($index))?></td>
		<td><div<?php if ($count['active'] > 0): ?> class="conversions"<?php endif; ?>><?=$count['active']?></div></td>
		<td><div<?php if ($count['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$count['cancelled']?></div></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<hr >

<table class="fancy wide js-sortable">
	<thead><tr>
		<th>Geschlecht</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($gender as $index => $count): ?>
	<tr>
		<td><?=ucfirst($index)?></td>
		<td><div<?php if ($count['active'] > 0): ?> class="conversions"<?php endif; ?>><?=$count['active']?></div></td>
		<td><div<?php if ($count['cancelled'] > 0): ?> class="cancelled"<?php endif; ?>><?=$count['cancelled']?></div></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
</div>


<table class="fancy js-sortable">
	<thead><tr>
		<th>Ressort</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($ressorts as $index => $count): ?>
	<tr>
		<td><a href="/ressort/<?=$index?>"><?=ucfirst($index)?></a></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<div>
<table class="fancy wide js-sortable">
	<thead><tr>
		<th>Artikel-Kategorien</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($types as $index => $count): ?>
	<tr>
		<td><a href="/type/<?=urlencode(str_replace('/', '-slash-', $index))?>"><?=$index?></a></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

<table class="fancy wide js-sortable">
	<thead><tr>
		<th>Artikel-Tags</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($tags as $index => $count): ?>
	<tr>
		<td><a href="/type/<?=urlencode(str_replace('/', '-slash-', $index))?>"><?=$index?></a></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
</div>

</section>

<hr />


<section class="detail-layout" style="align-items:start">

<table class="fancy js-sortable">
	<thead><tr>
		<th>Stadt</th>
		<th>Käufe</th>
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
		<th>Käufe</th>
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

<?php if (auth_rights('author')): ?>
<table class="fancy js-sortable">
	<thead><tr>
		<th>Autor</th>
		<th>Käufe</th>
		<th>Gekündigt</th>
		<th>Quote</th>
	</tr></thead>
	<tbody>
<?php foreach ($authors as $index => $count): ?>
	<tr>
		<td><a href="/author/<?=urlencode(str_replace('/', '-slash-', $index))?>"><?=$index?></a></td>
		<td><?=$count['active']?></td>
		<td><?=$count['cancelled']?></td>
		<td><?=round($count['cancelled'] / $count['active'] *100,2)?> %</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>

</section>

<hr />

<h1>Conversion Informationen</h1>

<details>
<summary>alle registrierten Käufe auflisten</summary>

<section>


<?php if ($conversions): ?>
<table class="fancy mb wide js-sortable">
<thead>
<tr class="text-right">
	<th>Art-ID</th>
	<th>Trans-ID</th>
	<th>Artikel-Typ</th>
	<th>Ressort</th>
	<?php if (auth_rights('author')): ?><th>Author</th><?php endif; ?>
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
	<td><a href="/artikel/<?=$conversion['article_id']?>"><?=$conversion['article_id']?></a></td>
	<td><?=$conversion['transaction_id']?></td>
	<td><?=$conversion['article_type']?></td>
	<td><?=$conversion['article_ressort']?></td>
	<?php if (auth_rights('author')): ?><td><a href="/author/<?=urlencode(str_replace('/', '-slash-', $conversion['article_author']))?>"><?=$conversion['article_author']?></a></td><?php endif; ?>
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

</section>

</main>
