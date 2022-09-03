<main>

<?php include tpl('navigation/date-picker');?>

<h1>
<?=$page['title']?>
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


<div class="col-2" style="grid-template-columns: 1fr 1fr;">

	<figure class="mb">
		<h3 class="text-center">Artikel Produktion im Zeitverlauf</h3>
		<?=$charts->get('articles_by_date');?>
	</figure>


	<figure class="mb">
		<h3 class="text-center">Plusartikelquote im Zeitverlauf</h3>
		<?=$charts->get('plusquote_by_date');?>
	</figure>

</div>

<hr>

<div>

	<h1 class="text-center">Sortiert nach Audiences</h1>

	<figure class="mb" style="margin-bottom:3em;">
		<h3 class="text-center">Audiences nach Wochentag</h3>
		<?=$charts->get('article_production_by', ['audience','weekday']);?>
	</figure>

	<figure class="mb" style="margin-bottom:3em;">
		<h3 class="text-center">Audiences nach Uhrzeit</h3>
		<?=$charts->get('article_production_by', ['audience','hour']);?>
	</figure>

	<figure class="mb" style="margin-bottom:3em;">
		<h3 class="text-center">Audiences im Verlauf</h3>
		<?=$charts->get('article_production_by', 'audience');?>
	</figure>

</div>

<hr>

<div>

	<h1 class="text-center">Sortiert nach Themen</h1>

	<figure class="mb" style="margin-bottom:3em;">
		<h3 class="text-center">Themen nach Wochentag</h3>
		<?=$charts->get('article_production_by', ['type','weekday']);?>
	</figure>

	<figure class="mb" style="margin-bottom:3em;">
		<h3 class="text-center">Themen nach Uhrzeit</h3>
		<?=$charts->get('article_production_by', ['type','hour']);?>
	</figure>	

	<figure class="mb" style="margin-bottom:3em;">
		<h3 class="text-center">Themen im Verlauf</h3>
		<?=$charts->get('article_production_by', 'type');?>
	</figure>


</div>

</main>
