<main>

<?php include tpl('navigation/date-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?></h1>
<?php endif; ?>


<p>Die Conversiondaten stimmen sei 23. März (PlenigoV3 Umstellung). <b>Kündigungsdaten sind NICHT Live</b> und beziehen sich auf den Tag der letzten Statistik-Aktualisierung des Artikels. Conversions des heutigen Tages werden noch nicht aufgeführt!
</p>


<p class="light-box" style="margin-bottom:2em;">
Gesamtbestellungen: <b><?=$numberOfOrders?></b>
&emsp; davon Plusseite: <b class="blue"><?=$plusOnly?></b>
&emsp; davon Extern: <b class="blue"><?=$externalOnly?></b>
&emsp; davon Gekündigt: <b class="redish"><?=$numberOfCancelled?></b>
&emsp; Kündigerquote: <b class="orange"><?=$cancelQuote?>&thinsp;%</b>
&emsp; ⌀-Haltedauer: <b class="blue"><?=number_format($averageRetention,2,',','.')?> Tage</b>
</p>

<hr>

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

		<h3>Preis</h3>
		<?php $tableData = $price;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>

		<h3>Stadt (laut Plenigo)</h3>
		<?php $tableData = $city;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>

	</div>

	<div>
		<h3>Ressorts</h3>
		<?php $tableData = $ressorts;?>
		<?php $tableName = 'Ressort';?>
		<?php include tpl('orders/stats-table');?>

		<h3>Postleitzahlen</h3>
		<?php $tableData = $plz;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>



		<h3>Referrer (aktuell deaktiviert)</h3>
		<?php $tableData = $source;?>
		<?php $tableName = null;?>
		<?php include tpl('orders/stats-table');?>


	</div>

	<div>
		<h3>Inhaltstypen</h3>
		<?php $tableData = $type;?>
		<?php $tableName = 'Type';?>
		<?php include tpl('orders/stats-table');?>

		<h3>Tags</h3>
		<?php $tableData = $tag;?>
		<?php $tableName = 'Tag';?>
		<?php include tpl('orders/stats-table');?>
	</div>

	<div>



	</div>

</section>



</main>
