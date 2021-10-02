<main>

<?php include tpl('navigation/date-picker');?>

<h1>
Artikel Entwicklung nach Publikationsdatum
</h1>

<hr/>

	<figure class="mb">
		<h3 class="text-center">Artikel im Zeitverlauf</h3>
		<?=$charts->get('articlesByDate');?>
	</figure>


<div class="col-2" style="grid-template-columns: 2fr 1fr;">


	<figure>
		<h3 class="text-center">Artikel nach Uhrzeit</h3>
		<?=$charts->get('articlesByTime');?>
	</figure>

	<figure>
		<h3 class="text-center">Artikel nach Wochentag</h3>
		<?=$charts->get('articlesByWeekday');?>
	</figure>


</div>

</main>
