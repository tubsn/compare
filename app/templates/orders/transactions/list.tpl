<main class="">


<?php include tpl('navigation/month-picker');?>

<?php if ($page['title']): ?>
<h1><?=$page['title']?>
&ensp;<span style="color: #fff; background-color: #688c66; padding: 0 .2em 0 0; border-radius: .2em;">
+&thinsp;<?=number_format($revenue, 2,',','.')?>&thinsp;€
</span>

</h1>
<?php endif; ?>


<p class="light-box" style="margin-bottom:1em; width:100%;">
Bestellungen: <b class="conversions"><?=$orders?></b>
&emsp; Erfolgreiche Transaktionen: <b class=""><?=gnum(count($clusters['Erfolgreiche Transaktionen']))?></b>
&emsp; Jahresabos: <b class="orange"><?=$yearly?></b>
&emsp; Kunden mit Problemen: <b class="blue" title="Gesamt Fehler: <?=count($clusters['Fehlgeschlagene Transaktionen'])?>"><?=$customers['Fehlgeschlagene Transaktionen']?></b>
&emsp; Problem-Quote: <b class="blue"><?=percentage($customers['Fehlgeschlagene Transaktionen'],count($clusters['Erfolgreiche Transaktionen']))?>&thinsp;%</b>
&emsp; Rückbuchungen: <b class="redish"><?=count($clusters['Rückzahlungen'])?></b>
&emsp; Rückbuchungs-Quote: <b class="redish"><?=percentage(count($clusters['Rückzahlungen']),count($clusters['Erfolgreiche Transaktionen']))?>&thinsp;%</b>
&emsp; durch KSE: <b class="redish"><?=$kse?></b>
</p>


<hr>

<section style="margin-bottom:2em; display:flex; width:100%; gap: 1em; align-items:start; justify-content: space-between;">

<div>
	<h3>Umsatzdaten des Monats</h3>
	<table class="compact fancy" style="padding:1em 1.2em; background-color: #eef0f2;"> 

	<tr>
		<td>Eingänge:</td>
		<td><?=number_format($payments, 2,',','.')?>&thinsp;€</td>
	</tr>
	<tr>
		<td>Rückzahlungen:</td>
		<td class="red"><?=number_format($chargeback, 2,',','.')?>&thinsp;€</td>
	</tr>
	<tr>
		<td>Gebühren (ungefähr):</td>
		<td class="red"><?=number_format($costs, 2,',','.')?>&thinsp;€</td>
	</tr>	
	<tr>
		<td><em>Geschätzter Umsatz:</em></td>
		<td><em><b><?=number_format($revenue, 2,',','.')?>&thinsp;€</b></em></td>
	</tr>
	</table>

	<?php if (auth_rights('invoice')): ?>
		<a class="button" href="/invoices/<?=$selectedMonth ?? null?>">Rechnungen als Excel herunterladen</a>
	<?php endif ?>

</div>


<div class="transactions">
	<h3>Kunden mit mehr als einer Abbuchung&nbsp;(<?=count($duplicates ?? [])?>)</h3>
	<p>Nutzer die möglicherweise unbeabsichtigt mehrere Abos besitzen</p>
	<ul>
		<?php foreach ($duplicates as $ID => $count): ?>
		<li<?=$count > 1 ? ' class="danger"' : null ?>>
			<a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/customers/<?=$ID?>/show"><?=$ID?></a> <?=$count > 1 ? '('.($count+1).')' : null ?>
		</li>
		<?php endforeach ?>
	</ul>
</div>

<div class="transactions">
	<h3>Kunden mit mehrfachen Rückzahlungen&nbsp;(<?=count($duplicateChargebacks ?? [])?>) </h3>
	<p>Nutzer die uns potentiell mit Absicht betrügen...</p>
	<ul>
		<?php foreach ($duplicateChargebacks as $ID => $count): ?>
		<li<?=$count > 1 ? ' class="danger"' : null ?>><a target="_blank" href="https://backend.plenigo.com/<?=PLENIGO_COMPANY_ID?>/customers/<?=$ID?>/show"><?=$ID?></a> <?=$count > 1 ? '('.($count+1).')' : null ?></li>
		<?php endforeach ?>
	</ul>
</div>

<style>
	.transactions {text-align: center;}
	.transactions ul {list-style-type: none; margin:0; padding:1em 1.2em; background-color: #eef0f2; column-count: 1;}
	
	@media only screen and (min-width: 1100px) {.transactions ul {column-count: 2;}}
	@media only screen and (min-width: 1400px) {.transactions ul {column-count: 3;}}
	@media only screen and (min-width: 1920px) {.transactions ul {column-count: 4;}}

	.transactions ul li {margin:0 padding:0; padding:.2em 0.4em; background-color:#486688; border-radius:5px; margin-bottom:.2em; color:white; display: block;}
	.transactions ul li.danger {background-color: #ac4c4c}
	.transactions ul li a {text-decoration:none; color:white;}

	details summary {padding: 0.2em 0.8em; background-color: #eef0f2;}
	details:hover summary {background-color: #dae2ea;}

	details[open] summary {background-color: #ece5d4; font-weight: bold;}

</style>

</section>



<section style="margin-bottom:3em; margin-top:2em;">
<h3>Transaktionen im Detail <em>(Gesamtvorgänge: <?=gnum(count($clusters['Transaktionen']))?>)</em></h3>
<?php foreach ($clusters as $title => $transactions): ?>
<?php if ($title == 'Transaktionen'): {continue;} ?><?php endif ?>

<?php if ($transactions): ?>
<details>

	<?php
	$paypal = array_count_by_value('PAYPAL', 'paymentProvider', $transactions);
	$stripe = array_count_by_value('STRIPE', 'paymentProvider', $transactions);
	$bank = array_count_by_value('BANK_ACCOUNT', 'paymentMethod', $transactions);
	$credit = array_count_by_value('CREDIT_CARD', 'paymentMethod', $transactions);
	?>

	<summary><?=str_replace('Fehlgeschlagene Transaktionen', 'Transaktionen mit Problemen', $title)?>: <?=gnum(count($transactions))?></summary>

	<p class="light-box" style="margin-bottom:1em;">
	Transaktionen: <b><?=count($transactions)?></b>
	&emsp; einzelne Kunden: <b><?=$customers[$title]?></b>
	&emsp; Paypal: <b class="blue"><?=$paypal?></b>
	&emsp; Stripe: <b style="color:#b587c4"><?=$stripe?></b>
	&emsp; SEPA: <b style="color:#b587c4"><?=$bank?></b>
	&emsp; Kreditkarte: <b style="color:#b587c4"><?=$credit?></b>

	<?php if ($title == 'Rückzahlungen'): ?>
	&emsp; Rückzahlung ausgelöst durch KSE: <b class="redish"><?=$kse?></b>
	<?php endif ?>

	<?php if ($title == 'Erfolgreiche Transaktionen'): ?>
	&emsp; >1Euro: <b><?=$fullprice?></b>
	<?php endif ?>

	</p>

	<?include tpl('orders/transactions/list-table');?>
</details>

<?php else: ?>
	<details>
		<summary><?=$title?>: 0</summary>		
	</details>
<?php endif; ?>
<?php endforeach ?>


</section>

<section style="margin-bottom:3em;">
<h3>Einnahmen-Entwicklung <em>(in Euro)</em></h3>
<p>Achtung: da wir die Gebühren nicht 100% berechnen können werden hier nur die Einnahmen abzüglich der Rückzahlungen dargestellt!</p>
<figure class="mb">
	<?=$charts->create([
		'metric' => $kpis['revenue'],
		'dimension' => $kpis['dimensions'],
		'color' => '#688c66',
		'height' => 450,
		'stacked' => true,
		'showValues' => false,
		'name' => 'Transaktions-Umsatz',
		'template' => 'charts/default_line_chart',
	]);?>
</figure>


</section>


<p class="light-box text-right" style="margin-bottom:3em"> 
	<b>Ansicht filtern: </b>
<a  href="/transactions?month=<?=$selectedMonth?>&paypal">nur Paypal</a> - 
<a  href="/transactions?month=<?=$selectedMonth?>&stripe">nur Stripe</a> - 
<a  href="/transactions?month=<?=$selectedMonth?>">alles anzeigen</a>
</p>




</main>
