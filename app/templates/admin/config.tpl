<main class="settings-page">
<h1>Einstellungen</h1>

<div class="settings-layout">

<section class="mb">

	<h3>Nutzerverwaltung</h3>
	<p>Neue Nutzer angelegen oder verwalten.<br /><br />
	<a class="button noline" href="/admin/users">zur Userverwaltung</a></p>

<hr />

	<h3>Zusätzliche Dashboards / Newsletter</h3>
	<p>Übersicht zu laufenden UTM Kampagnen und Newsletter Previews
		<div style="display:flex; gap:5px; align-items: start; margin-bottom:.5em;">
			<a class="button noline" href="/export/campaigns/30">UTM Kampagnen Übersicht</a>
			<a class="button noline" href="/campaigns/fbaccelerator">Shop - Accelerator</a>
			<a class="button noline" href="/newsletter/chefredaktion">Chefredaktion Newsletter</a>
		</div>
		<div style="display:flex; gap:5px; align-items: start;">
			<a class="button noline" href="/newsletter/sport">Sport Newsletter</a>
			<a class="button noline" href="/newsletter/nachdrehalert">Nachdreh-Alert</a>
			<a class="button noline" href="/newsletter/nachdrehalert-score">Nachdreh-Alert Weekly</a>
			<a class="button noline" href="https://abo.lr-digital.de/auswertung">Newsletter Auswertung</a>
		</div>
	</p>
<hr />


<h3>Artikel-Importieren</h3>

<p>Normalerweise werden RSS-Feeds einmal täglich um 0.30 Uhr morgens importiert. Hier kann der Import manuell gestartet werden.</p>
<p>Zur Zeit werden folgende Feeds importiert:</p>

<small><pre style="line-height:1.2">
<?php foreach (IMPORT_FEEDS as $feedname): ?>
<?=$feedname?>

<?php endforeach; ?>
</small></pre>

<p><a class="button noline" href="/admin/import">RSS-Feeds Importieren</a></p>

<hr />


<h3>Daten-Exports</h3>
<p>
Hier können Artikel, Conversions und Kampagnendaten inklusive Klick- und Kündigerstatistik als Excel-Datei exportiert werden.
<div style="display:flex; gap:5px; align-items: start;">
	<a class="button noline" href="/export/articles">Artikeldaten</a>
	<a class="button" href="/export/conversions">Conversiondaten</a>
	<a class="button" href="/export/kpis">Portaldaten</a>
	<a class="button" href="/export/campaigns">UTM-Kampagnen</a>
</div>
</p>



</section>

<section>
	<h3>Audiences, Themencluster und Tags festlegen</h3>
	<a href="/admin/cluster" class="button mb">Cluster-Manager öffnen (Themen verschieben)</a>
	<p>Hier werden mögliche Artikel Cluster eingestellt. Die Cluster können gelöscht werden, bereits gesetzte Artikel behalten ihren Typen bei. Für neue Artikel ist der gelöschte Typ nicht mehr auswählbar.</p>


	<h3>Themen:</h3>
	<form class="mb" action="" method="post">
	<textarea name="typen" class="artikel-typen"><?=$typen?></textarea>

	<h3>Audiences:</h3>
	<textarea name="audiences" class="artikel-audiences mt"><?=$audiences?></textarea>

	<h3>#-Hashtags:</h3>
	<textarea name="tags" class="artikel-tags mt"><?=$tags?></textarea>
	<button type="submit">Cluster-Einstellungen speichern</button>
	</form>

</section>

<section>

	<h3>Plenigo Bestelldaten Importieren</h3>
	<p>Die Bestelldaten für Conversions und Kündiger werden täglich um 5 Uhr morgens aktualisiert.</p>

	<a class="button noline" href="/admin/orders">Import Manager öffnen</a>
	</p>

<hr>

	<h3>Drive Usersegmente Importieren</h3>
	<p>Hier werden aus den Drive Tabellen alle Usersegmente von Käufern und Kündigern der letzten 3 Tage importiert.</p>

	<a class="button noline" href="/admin/warmup/readers">Drive Segmente importieren</a>
	</p>

<hr>

	<h3>Referer Mapping</h3>
	<p>Alle Conversions werden anhand einer Mapping Tabelle zu einem passenden Referer Typen zugeordnet.</p>

	<a class="button noline" href="/admin/warmup/assign-sources">Conversion Referals mappen</a>
	</p>

<hr>

	<h3>Linkpulse - Subscriber Daten</h3>
	<p>Subscriberdaten werden täglich um 3 Uhr morgens für alle Artikel der letzten 3 Tage, aktualisiert.</p>

	<form method="get" action="/admin/warmup/subscribers">
		<fieldset class="col-2">
		<label>von:
			<input type="date" name="from">
		</label>
		<label>bis:
			<input type="date" name="to">
		</label>
	</fieldset>
	<button class="mb" type="submit">Subscriber-Daten aktualisieren</button>
	</form>

<hr>

	<h3>Analytics - Kaufreiz Daten (Paywallklicks)</h3>
	<p>Kaufreiz werden täglich um 3 Uhr morgens für alle Artikel der letzten 3 Tage, aktualisiert.</p>

	<form method="get" action="/admin/warmup/buyintentions">
		<fieldset class="col-2">
		<label>von:
			<input type="date" name="from">
		</label>
		<label>bis:
			<input type="date" name="to">
		</label>
	</fieldset>
	<button class="mb" type="submit">Kaufreiz-Daten aktualisieren</button>
	</form>


<hr>

	<h3>Google Analytics - Statistik Daten</h3>
	<p>Die Analytics Daten werden täglich um 3 Uhr morgens für alle Artikel der letzten 3 Tage, aktualisiert.
		Um 4 Uhr werden zusätzlich Artikel der letzten Woche erneuert. Artikel die älter als eine Woche sind werden NICHT automatisch aktualisiert.
	</p>

	<p class="box">Achtung: wenn möglich nicht mehr als eine Woche auswählen! die Google Abfrage ist sehr Zeitaufwendig. Wir können kostenlos nur 50000 Artikel pro Monat abfragen.</p>

	<form method="get" action="/admin/warmup">
		<fieldset class="col-2">
		<label>von:
			<input type="date" name="from">
		</label>
		<label>bis:
			<input type="date" name="to">
		</label>
	</fieldset>
	<button class="mb" type="submit">Analytics-Daten aktualisieren</button>
	</form>



</section>

</div>
</main>
