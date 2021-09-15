<main>

<?php include tpl('navigation/date-picker');?>

<h1>
Artikel Entwicklung
</h1>

<hr/>

	<figure class="mb">
		<h3 class="text-center">Artikelproduktion nach Monat</h3>
		<?=$charts->get('articlesByTime');?>
	</figure>

</main>
