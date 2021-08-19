<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>

<p>
Die Bestelldaten beziehen sich auf den <b>betrachteten Zeitraum</b> und nicht auf das Publikationsdatum des Artikels.
Es kommt daher zu Abweichungen im Vergleich zur Statistik-Seite (dort wird nach allen Conversions nach lebensdauer Artikel Publikationsdatum gemessen).
<b>Beispiel:</b> Ein Artikel vom 1. Mai hat drei Conversions, zwei im Mai und eine später im Juni. In den Bestellstatistiken werden im Zeitraum Mai zwei Conversions ausgegeben. In der Artikelstatistik jedoch drei, da der Artikel, der im Mai Publiziert wurde insgesamt drei Conversions erhalten hat.
</p>

<p>Die Bestellungen werden seit <b>23. März</b> direkt aus dem SSO System (Plenigo) bezogen. Zur Zeit, die beste Datenquelle die wir haben!<br /> Die <b>Kündigungsdaten sind NICHT Live</b> sie beziehen sich auf den Tag der letzten Statistik-Aktualisierung. Conversions des heutigen Tages werden bis 3 Uhr morgens aufgeführt!
</p>

<p>
<a class="button" href="/orders/list">Bestellungen einzeln auflisten</a>&emsp;
<a class="button" href="/orders/today">Live Conversions aus Plenigo</a>
</p>

<p class="light-box" style=" margin-bottom:1em; margin-top:1em;">
Gesamtbestellungen: <b class="conversions"><?=$numberOfOrders?></b>
&emsp; davon Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; davon Extern: <b class="blue"><?=$externalOnly?></b>
&emsp; davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=$cancelQuote?>&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>


<h3>Bestellverteilung nach Ressort: </h3>
<?php include tpl('charts/bar_chart');?>


<section class="detail-layout" style="align-items:start">

	<div>
		<h3>Bezahlarten</h3>
		<?php $tableData = $payment;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>

		<h3>Geschlecht</h3>
		<?php $tableData = $gender;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>

		<h3>Produkt</h3>
		<?php $tableData = $product;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>

		<?php if (count($internalTitle) > 0): ?>
		<h3>Interner Produktname</h3>
		<?php $tableData = $internalTitle;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>
		<?php endif; ?>

		<?php if (count($price) > 0): ?>
		<h3>Preis</h3>
		<?php $tableData = $price;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>
		<?php endif; ?>

		<?php if (count($city) > 0): ?>
		<h3>Stadt (laut Plenigo)</h3>
		<?php $tableData = $city;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>
		<?php endif; ?>

	</div>

	<div>
		<h3>Ressorts</h3>
		<?php $tableData = $ressorts;?>
		<?php $tableName = 'Ressort';?>
		<?php include tpl('orders/stats-table');?>

		<?php if (count($plz) > 0): ?>

		<h3>Postleitzahlen (laut Plenigo)</h3>
		<?php $tableData = $plz;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>
		<?php endif; ?>

	</div>

	<div>

		<?php if (count($type) > 0): ?>
		<h3>Artikel nach Inhaltstypen</h3>
		<?php $tableData = $type;?>
		<?php $tableName = 'Type';?>
		<?php include tpl('orders/stats-table');?>
		<?php endif; ?>

		<?php if (count($tag) > 0): ?>

		<h3>Artikel Tags / Dossiers</h3>
		<?php $tableData = $tag;?>
		<?php $tableName = 'Tag';?>
		<?php include tpl('orders/stats-table');?>
		<?php endif; ?>

		<?php if (count($source) > 0): ?>

		<h3>Referer </h3>
		<?php $tableData = $source;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>
		<?php endif; ?>

	</div>

	<div>



	</div>

</section>

</main>
