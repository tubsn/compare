<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>

<p>Es werden nur Klicks auf selbstproduzierte Artikel gezählt. Übersichtsseiten und Artikel mit DPA-Kürzel werden ignoriert.<br/>
Die Daten werden <b>auf Lebensdauer des Artikels</b> gruppiert. Der eingestellte Zeitraum filtert das <b>Publikationsdatum der Artikel!</b> Nicht die eigentlichen Daten!</p>
<p><b>Kündigungsdaten sind NICHT Live</b> und beziehen sich auf den Tag der letzten Statistik-Aktualisierung des Artikels (i.d.R. 3 Tagen ach Publikation). <br />
</p>


<p class="light-box" style="margin-bottom:2em;">
Artikel: <b title="davon Plusartikel: <?=$plusarticles?>"><?=$articles?></b> &emsp; Pageviews: <b class="blue"><?=number_format($pageviews,0,',','.')?></b>
<?php if ($articles > 0): ?>
&emsp; ⌀-Pageviews: <b class="blue"><?=number_format(($pageviews / $articles), 0,',','.') ?></b>
<?php endif ?>
&emsp; Subscriber Views: <b class="deepblue"><?=number_format($subscribers,0,',','.')?></b>
&emsp; Mediatime: <b class="green"><?=number_format($mediatime,0,',','.')?>s</b>
&emsp; Besuche: <b class="blue"><?=number_format($sessions,0,',','.')?></b>
&emsp; Kaufimpulse: <b class="orange"><?=$buyintents?></b>
&emsp; Conversions: <b class="conversions"><?=$conversions?></b>
&emsp; Gekündigt: <b class="redish"><?=$cancelled?></b>
</p>



<div style="display:grid; grid-template-columns: 1fr 1fr 1fr; grid-gap:2vw;">

	<figure class="">
		<h3 class="text-center">Anzahl Produzierte Artikel</h3>
		<?=$charts->get('articlesByRessort'); ?>
	</figure>

	<figure class="">
		<h3 class="text-center">Gesamt Mediatime nach Ressort</h3>
		<?=$charts->get('fullMediatimeByRessort'); ?>
	</figure>

		<figure class="">
			<h3 class="text-center">Conversionrate nach Ressort</h3>
			<?=$charts->get('conversionRateByRessort'); ?>
		</figure>

</div>


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

	<div style="display:grid; grid-template-columns: 1fr 1fr; grid-gap:2vw;">

		<figure class="">
			<h3 class="text-center">Subscriber Quote nach Ressort</h3>
			<?=$charts->get('subscriberQuoteByRessort'); ?>
		</figure>

		<figure class="">
			<h3 class="text-center">Durchschnittliche Pageviews nach Ressort</h3>
			<?=$charts->get('avgPageviewsByRessort'); ?>
		</figure>

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

	<div style="display:grid; grid-template-columns: 1fr 1fr; grid-gap:2vw;">

		<figure class="">
			<h3 class="text-center">Subscriber Quote nach Inhaltstyp</h3>
			<?=$charts->get('subscriberQuoteByType'); ?>
		</figure>

		<figure class="">
			<h3 class="text-center">Durchschnittliche Pageviews nach Inhaltstyp</h3>
			<?=$charts->get('avgPageviewsByType'); ?>
		</figure>

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

	<div style="display:grid; grid-template-columns: 1fr 1fr; grid-gap:2vw;">

		<figure class="">
			<h3 class="text-center">Subscriber Quote nach #-Tag</h3>
			<?=$charts->get('subscriberQuoteByTag'); ?>
		</figure>

		<figure class="">
			<h3 class="text-center">Durchschnittliche Pageviews nach #-Tag</h3>
			<?=$charts->get('avgPageviewsByTag'); ?>
		</figure>

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

</main>
