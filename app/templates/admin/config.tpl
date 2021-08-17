<main class="settings-page">
<h1>Content Datenbank Einstellungen</h1>

<div class="settings-layout">

<section>

	<h3>Nutzer - Verwaltung</h3>
	<p>Neue Nutzer angelegen oder verwalten.<br /><br />
	<a class="button noline" href="/admin/users">zur Userverwaltung</a></p>

<hr />

<h3>Artikel-Import Verwaltung</h3>

<p>Normalerweise werden RSS-Feeds einmal täglich um 0.30 Uhr morgens importiert. Hier kann der Import manuell gestartet werden.</p>
<p>Zur Zeit werden folgende Feeds importiert:</p>

<small><pre style="line-height:1.2">
<?php foreach (IMPORT_FEEDS as $feedname): ?>
<?=$feedname?>

<?php endforeach; ?>
</small></pre>

<p><a class="button noline" href="/admin/import">RSS-Feeds Importieren</a></p>

<hr>

<h3>Daten-Exports</h3>
<p>
Hier können alle Artikel inklusive Klick- und Kündigerstatistik exportiert werden.<br /><br />
<a class="button noline" href="/export/articles">Artikeldaten als Excel</a> <a class="button" href="/export/conversions">Conversiondaten als Excel</a></p>

<a class="button noline" target="_blank" href="/newsletter/chefredaktion">Export Chefredaktion</a>


</section>

<section>
	<h3>Artikeltypen und Subkategorien festlegen</h3>
	<p>Hier werden mögliche Artikel Inhaltstypen eingestellt. Die Typen können gelöscht werden, bereits gesetzte Artikel behalten ihren Typen bei. Für neue Artikel ist der gelöschte Typ nicht mehr auswählbar. (Nützlich für Zeitlich begrenzte Serien - Reihenfolge ist relevant)</p>
	<p>Artikeltypen:</p>

	<form class="mb" action="" method="post">
	<textarea name="typen" class="artikel-typen"><?=$typen?></textarea>

	<p>Subkategorien:</p>

	<textarea name="tags" class="artikel-tags mt"><?=$tags?></textarea>
	<button type="submit">Artikel Kategorien speichern</button>
	</form>


	<hr/>
	<h3>Plenigo Order Tabelle</h3>
	<p>Zwischenlösung um Plenigo API Daten zu Importieren</p>
	<a class="button noline" href="/admin/orders">Zur Order Import Verwaltung</a>



</section>

<section>

	<h3>Conversion und Kündigerdaten aktualisieren</h3>
	<p>Die Kündigerdaten werden täglich um 3 Uhr morgens für alle Käufe in Artikeln der letzten 3 Tage aktualisiert. Es werden zunächst Google Analytics und dann die Plenigo API abgefragt.</p>
	<a class="button noline" href="/admin/warmup_conversions">Conversions der letzten 3 Tage aktualisieren</a>
	</p>

<hr>


	<h3>Google Analytics - Statistik Daten</h3>
	<p>Die Statistikdaten werden täglich um 1 Uhr morgens für alle Artikel der letzten 3 Tage, aktualisiert.
		Um 2 Uhr werden zusätzlich alle Artikel der letzten Woche erneuert. Artikel die älter als eine Woche sind werden NICHT automatisch aktualisiert.
	</p>

<hr>

	<h3>Analytics-Klickdaten aktualisierungen</h3>
	<p>Achtung: Diese Abfrage dauert ca. 1-2 Minuten. Je nach Artikel Menge. (Durchschnittlich 300 Artikel pro Woche)</p>

	<p>
	<a class="button noline" href="/admin/warmup/1">letzte&nbsp;Woche</a>
	<a class="button noline" href="/admin/warmup/2">vorletzte&nbsp;Woche</a>
	<a class="button noline" href="/admin/warmup/3">vor&nbsp;3&nbsp;Wochen</a>
	</p>

<hr>

	<h3>Analytics-Daten für Zeitraum aktualisieren</h3>
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
