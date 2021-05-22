<main>

<?php include tpl('navigation/date-picker');?>

<h1>Allgemeine Statistiken</h1>

<p>Es werden nur Klicks auf selbstproduzierte Artikel gezählt, Übersichtsseiten, Bildergalerien, DPA-Artikel u.ä. sind außen vor.<br/>
Daten werden seit Lebensdauer des Artikels angezeigt. Der eingestellte Zeitraum filtert das <b>Publikationsdatum des Artikels!</b> Nicht die eigentlichen Daten! </p>
<p><b>Kündigungsdaten sind NICHT Live</b> und beziehen sich auf den Tag der letzten Statistik-Aktualisierung des Artikels.<br /> Auch hier handelt es sich um <b>Lebensdauerdaten</b> - der eingestellte Zeitraum filtert lediglich das Publikationsdatum des Artikels.<br />
Die Kündigerraten sind vor Februar 2021 nicht relevant, da hier zuviele Daten fehlen!</p>

<p>Alle Daten werden aus performance Gründen <b>1 Minute lang gecached.</b></p>

<p class="light-box" style="margin-bottom:2em;">
Artikel: <b><?=$articles?></b> &emsp; Klicks: <b class="blue"><?=number_format($pageviews,0,',','.')?></b> 
<?php if ($articles > 0): ?>
&emsp; ⌀-Klicks: <b class="blue"><?=number_format(($pageviews / $articles), 0,',','.') ?></b>
<?php endif ?>
&emsp; Besuche: <b class="blue"><?=number_format($sessions,0,',','.')?></b> &emsp; Conversions: <b class="orange"><?=$conversions?></b>
&emsp; Gekündigt: <b class="redish"><?=$cancelled?></b>
</p>

<section class="stats-layout">
	<div>
		<h1>Statistiken nach Ressort</h1>
		<?php if ($ressortStats): ?>

		<?php
		$tableData = $ressortStats;
		$tableName = 'Ressort';
		$urlPrefix = '/ressort/';
		include tpl('pages/stats-table');
		?>

		<?php else: ?>
		<h3>keine Artikel</h3>
		<?php endif; ?>
	</div>

	<div>
		<h1>Statistiken nach Inhaltstyp</h1>
		<?php if ($typeStats): ?>

		<?php
		$tableData = $typeStats;
		$tableName = 'Klasse';
		$urlPrefix = '/type/';
		include tpl('pages/stats-table');
		?>

		<?php else: ?>
		<h3>keine Artikel</h3>
		<?php endif; ?>
	</div>


	<div>
		<h1>Statistiken nach Artikel-Tag</h1>
		<?php if ($tagStats): ?>

		<?php
		$tableData = $tagStats;
		$tableName = 'Tag';
		$urlPrefix = '/tag/';
		include tpl('pages/stats-table');
		?>

		<?php else: ?>
		<h3>keine Artikel</h3>
		<?php endif; ?>
	</div>




<?php if (auth_rights('author')): ?>
	<div>
		<h1>Statistiken nach Autorenprofil</h1>
		<?php if ($authorStats): ?>

		<?php
		$tableData = $authorStats;
		$tableName = 'Autor';
		$urlPrefix = '/author/';
		include tpl('pages/stats-table');
		?>

		<?php else: ?>
		<h3>keine Artikel</h3>
		<?php endif; ?>
	</div>
<?php endif ?>

</section>

<div class="light-box mt" style="margin-bottom:2em;">
<h3>Legende / Begriffs Erklärungen:</h3>
<b>Plusquote</b> = Plusartikel / Artikel | <b>⌀-Klicks</b> = Klicks / Anzahl aller Artikel | <b>Plusleser %</b> Eingeloggte User / Klicks | <b>Conversionrate</b> = Conversions / Besuche | <b>Artikel bis Conversion</b> = Artikel / Conversions |
<b>K-Quote</b> = Kündigerquote (in der Regel auf 3 Tage)
</div>

</main>
