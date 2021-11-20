<main>

<?php include tpl('navigation/date-picker');?>

<h1>
Artikel Entwicklung nach Publikationsdatum
</h1>

<hr/>



<div class="col-2" style="grid-template-columns: 2fr 1fr;">


	<figure>
		<h3 class="text-center">Artikel nach Uhrzeit</h3>
		<?=$charts->get('articles_by_time');?>
	</figure>

	<figure>
		<h3 class="text-center">Artikel nach Wochentag</h3>
		<?=$charts->get('articles_by_weekday');?>
	</figure>


</div>

	<figure class="mb">
		<h3 class="text-center">Artikel im Zeitverlauf</h3>
		<?=$charts->get('articles_by_date');?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Anteil an Plusartikeln im Zeitverlauf</h3>
		<?=$charts->get('plusquote_by_date');?>
	</figure>


</main>
