
<h2>Dashboard Adminbereich:</h2>

<?php

$artliste = '';

foreach ($pageviews as $article) {
$artliste .= $article['pageviews'] . ' | ' . $article['title'] . ' | ' . PAGEURL . '/artikel/' . $article['id'] . ')%0D%0A';
}

$artliste .= '%0D%0A';

foreach ($conversions as $article) {
$artliste .= $article['conversions'] . ' | ' . $article['title'] . ' | ' . PAGEURL . '/artikel/' . $article['id'] . ')%0D%0A';
}


$body = 'Die Zahlen vom Vortag:
%0D%0A
%0D%0A
-Hier Bild einfügen-
%0D%0A
%0D%0A
%0D%0A' . $artliste . '
%0D%0A%0D%0A
alle Infos immer auf:
%0D%0A
https://reports.lr-digital.de';
?>

<div class="col-2 admin-board">
	<div>
		<h3>E-Mail Funktionen:</h3>
	<a class="button" href="mailto:lr.med.red@lr-online.de?subject=Das sind die Top-5-Artikel auf LR Online nach Reichweite und Conversions vom <?=date('d.m.Y', strtotime('today -1 day'))?>&body=<?=$body?>">E-Mail für Newsletter vorgenerieren</a>
	</div>

	<div>
		<h3>Zahlen Aktualisieren:</h3>
		<!--
	<a class="button" href="/admin/warmup/?from=<?=date('Y-m-d', strtotime('today -1 day'))?>&to=<?=date('Y-m-d', strtotime('today -1 day'))?>">Daten vom Vortag manuell aktualisieren</a>-->
	<a class="button" href="/admin/warmup_conversions/1"> Echtzeit Conversiondaten aktualisieren</a>
	<br> <small>(Achtung dauert ein paar Sekunden - Nur einmal drücken!)</small>
	</div>
</div>

<hr>

<h3>Linkliste zum Kopieren</h3>

<b>Klicks:</b><br>
<?php foreach ($pageviews as $article): ?>
<?=$article['pageviews']?> | <a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a> (ID: <?=$article['id']?>)<br/>
<?php endforeach ?>

<br>
<b>Conversions:</b><br>
<?php foreach ($conversions as $article): ?>
<?php if ($article['ressort'] == 'plus'): ?>
<?=$article['conversions']?> | Bestellungen über Shop- oder Plusseite<br/>
<?php else: ?>
<?=$article['conversions']?> | <a href="/artikel/<?=$article['id']?>"><?=$article['title']?></a> (ID: <?=$article['id']?>)<br/>
<?php endif; ?>
<?php endforeach ?>
