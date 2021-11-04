<main>

<?php include tpl('navigation/date-picker');?>

<h1><?=$page['title'] ?? 'Statistiken'?></h1>

<p>Es werden nur Klicks auf selbstproduzierte Artikel gezählt. Übersichtsseiten und Artikel mit DPA-Kürzel werden ignoriert.<br/>
Die Daten werden <b>auf Lebensdauer des Artikels</b> gruppiert. Der eingestellte Zeitraum filtert das <b>Publikationsdatum der Artikel!</b> Nicht die eigentlichen Daten!</p>
<p><b>Kündigungsdaten sind NICHT Live</b> und beziehen sich auf den Tag der letzten Statistik-Aktualisierung des Artikels (i.d.R. 3 Tagen ach Publikation). <br />
</p>

<p class="light-box" style="margin-bottom:2em;">
Artikel: <b title="davon Plusartikel: <?=$plusarticles?>"><?=$articles?></b> 
&emsp; Pageviews: <b title="Besuche: <?=number_format($sessions,0,',','.')?>" class="blue"><?=number_format($pageviews,0,',','.')?></b>
<?php if ($articles > 0): ?>
&emsp; ⌀-Pageviews: <b class="blue"><?=number_format(($pageviews / $articles), 0,',','.') ?></b>
<?php endif ?>
&emsp; Subscribers: <b class="deepblue"><?=number_format($subscribers,0,',','.')?></b>
<?php if ($articles > 0): ?>
&emsp; ⌀-Subscriber: <b class="deepblue"><?=number_format(($subscribers / $articles), 0,',','.') ?></b>
<?php endif ?>
&emsp; Mediatime: <b class="green"><?=number_format($mediatime,0,',','.')?>s</b>
&emsp; Kauf-Reize: <b class="orange"><?=$buyintents?></b>
&emsp; Conversions: <b class="conversions"><?=$conversions?></b>
&emsp; Kündiger: <b class="redish"><?=$cancelled?></b>
</p>


<section class="stats-layout">

	<div style="display:grid; grid-template-columns: 1fr 1fr; grid-gap:2vw;">

		<figure class="">
			<h3 class="text-center"><?=$chartOneTitle;?></h3>
			<?=$chartOne;?>
		</figure>

		<figure class="">
			<h3 class="text-center"><?=$chartTwoTitle;?></h3>
			<?=$chartTwo;?>
		</figure>

	</div>

	<div>
		<?php if ($groupedStats): ?>
		<?php include tpl('stats/stats-table');	?>
		<?php else: ?>
		<h3>keine Artikel vorhanden</h3>
		<?php endif; ?>
	</div>

</section>

</main>
